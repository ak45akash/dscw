<?php

namespace App\Services;

class HtmlSanitizer
{
    /**
     * @var list<string>
     */
    private array $allowedTags = [
        'p', 'br', 'hr',
        'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup',
        'ul', 'ol', 'li',
        'a', 'span', 'div',
        'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'pre', 'code',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'img', 'figure', 'figcaption',
    ];

    /**
     * @var list<string>
     */
    private array $allowedAttributes = [
        'href', 'title', 'target', 'rel',
        'src', 'alt', 'width', 'height', 'class',
        'colspan', 'rowspan',
    ];

    public function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $wrapped = '<?xml encoding="UTF-8"><div id="dscw-sanitize-root">'.$html.'</div>';
        $document->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('dscw-sanitize-root');
        if (! $root) {
            return '';
        }

        $this->cleanNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private function cleanNode(\DOMNode $node): void
    {
        if ($node instanceof \DOMElement) {
            $tag = strtolower($node->tagName);

            if (! in_array($tag, $this->allowedTags, true)) {
                $parent = $node->parentNode;
                if ($parent) {
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);
                }

                return;
            }

            if ($node->hasAttributes()) {
                $remove = [];
                foreach ($node->attributes as $attribute) {
                    $name = strtolower($attribute->name);
                    $value = $attribute->value;

                    if (! in_array($name, $this->allowedAttributes, true)) {
                        $remove[] = $attribute->name;
                        continue;
                    }

                    if (str_starts_with($name, 'on') || preg_match('/^\s*javascript:/i', $value)) {
                        $remove[] = $attribute->name;
                        continue;
                    }

                    if ($name === 'href' && preg_match('/^\s*(javascript|data):/i', $value)) {
                        $remove[] = $attribute->name;
                        continue;
                    }

                    if ($name === 'src' && ! preg_match('#^(https?:)?//|^/|^data:image/#i', $value)) {
                        $remove[] = $attribute->name;
                    }
                }

                foreach ($remove as $name) {
                    $node->removeAttribute($name);
                }
            }

            if ($tag === 'a' && $node->hasAttribute('href')) {
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }

        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            $this->cleanNode($child);
        }
    }
}
