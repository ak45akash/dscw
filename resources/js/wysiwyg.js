async function uploadFiles(uploadUrl, csrf, files) {
    const body = new FormData();
    Array.from(files).forEach((file) => body.append('images[]', file));

    const response = await fetch(uploadUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            Accept: 'application/json',
        },
        body,
    });

    const payload = await response.json();
    if (!response.ok) {
        throw new Error(payload.message || 'Image upload failed.');
    }

    return payload.urls || [];
}

async function uploadSingle(uploadUrl, csrf, file) {
    const body = new FormData();
    body.append('file', file);

    const response = await fetch(uploadUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            Accept: 'application/json',
        },
        body,
    });

    const payload = await response.json();
    if (!response.ok || !payload.location) {
        throw new Error(payload.message || 'Image upload failed.');
    }

    return payload.location;
}

function insertImages(quill, urls) {
    let index = quill.getSelection(true)?.index ?? quill.getLength();

    urls.forEach((url) => {
        quill.insertEmbed(index, 'image', url, 'user');
        index += 1;
        quill.insertText(index, '\n', 'user');
        index += 1;
    });

    quill.setSelection(index, 0, 'silent');
}

function toolbarFor(profile) {
    if (profile === 'full') {
        return [
            [{ header: [2, 3, 4, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['link', 'image'],
            ['blockquote', 'code-block'],
            ['clean'],
        ];
    }

    return [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link'],
        ['clean'],
    ];
}

async function loadQuill() {
    const [{ default: Quill }] = await Promise.all([
        import('quill'),
        import('quill/dist/quill.snow.css'),
    ]);

    return Quill;
}

export function registerWysiwygEditor(Alpine) {
    Alpine.data('wysiwygEditor', (config) => ({
        editor: null,
        async init() {
            const textarea = this.$el.querySelector(`#${CSS.escape(config.id)}`);
            const host = this.$el.querySelector('[data-quill-host]');

            if (!textarea || !host || host.dataset.quillReady === '1') {
                return;
            }

            host.dataset.quillReady = '1';

            const Quill = await loadQuill();
            const isFull = config.profile === 'full';
            const sync = () => {
                const html = this.editor.root.innerHTML;
                textarea.value = html === '<p><br></p>' ? '' : html;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                textarea.dispatchEvent(new Event('change', { bubbles: true }));
            };

            this.editor = new Quill(host, {
                theme: 'snow',
                placeholder: 'Write content…',
                modules: {
                    toolbar: {
                        container: toolbarFor(config.profile),
                        handlers: isFull
                            ? {
                                  image() {
                                      const input = document.createElement('input');
                                      input.type = 'file';
                                      input.accept = 'image/jpeg,image/png,image/gif,image/webp';
                                      input.multiple = true;
                                      input.addEventListener('change', async () => {
                                          if (!input.files?.length) {
                                              return;
                                          }

                                          try {
                                              const urls = await uploadFiles(
                                                  config.uploadUrl,
                                                  config.csrf,
                                                  input.files
                                              );
                                              insertImages(this.quill, urls);
                                              const errorEl = host.closest('.wysiwyg-field')?.querySelector('[data-quill-error]');
                                              if (errorEl) {
                                                  errorEl.hidden = true;
                                                  errorEl.textContent = '';
                                              }
                                          } catch (error) {
                                              const errorEl = host.closest('.wysiwyg-field')?.querySelector('[data-quill-error]');
                                              if (errorEl) {
                                                  errorEl.textContent = error.message || 'Could not upload images.';
                                                  errorEl.hidden = false;
                                              }
                                          }
                                      });
                                      input.click();
                                  },
                              }
                            : {},
                    },
                },
            });

            if (textarea.value.trim() !== '') {
                this.editor.root.innerHTML = textarea.value;
            }

            this.editor.on('text-change', sync);
            sync();

            if (isFull) {
                this.editor.root.addEventListener('paste', async (event) => {
                    const items = Array.from(event.clipboardData?.items || []);
                    const imageItems = items.filter((item) => item.type.startsWith('image/'));
                    if (!imageItems.length) {
                        return;
                    }

                    event.preventDefault();
                    try {
                        const urls = [];
                        for (const item of imageItems) {
                            const file = item.getAsFile();
                            if (file) {
                                urls.push(await uploadSingle(config.uploadUrl, config.csrf, file));
                            }
                        }
                        insertImages(this.editor, urls);
                    } catch (error) {
                        const errorEl = this.$el.querySelector('[data-quill-error]');
                        if (errorEl) {
                            errorEl.textContent = error.message || 'Could not upload images.';
                            errorEl.hidden = false;
                        }
                    }
                });
            }

            this.$el.closest('form')?.addEventListener('submit', sync);
        },
        destroy() {
            this.editor = null;
        },
    }));
}
