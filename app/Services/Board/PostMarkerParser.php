<?php

namespace App\Services\Board;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use InvalidArgumentException;

class PostMarkerParser
{
    public function __construct(private ForumFormatter $formatter) {}

    /** @return list<array{text: string}|array{marker: string}> */
    public function segments(string $message): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $message);
        preg_match_all('~\[/?(?:transfer|action|poll|scene)\b[^\]\r\n]*(?:\]|$)~im', $text, $matches, PREG_OFFSET_CAPTURE);
        if ($matches[0] === []) {
            return [['text' => $message]];
        }

        $document = new DOMDocument;
        $document->loadXML($this->formatter->parse($text), LIBXML_NONET);
        if ($document->documentElement->textContent !== $text) {
            throw new InvalidArgumentException('Dieser Text kann nicht sicher geteilt werden. Bitte prüfe seine Formatierung.');
        }
        $offset = 0;
        $ranges = [];
        $this->markupRanges($document->documentElement, $offset, $ranges);
        $segments = [];
        $position = 0;
        foreach ($matches[0] as [$token, $start]) {
            $end = $start + strlen($token);
            $enclosing = array_filter($ranges, fn ($range) => $start >= $range['start'] && $end <= $range['end']);
            if (array_any($enclosing, fn ($range) => $range['literal'])) {
                continue;
            }
            if ($enclosing !== []) {
                throw new InvalidArgumentException('Ein Baustein muss außerhalb von BBCode-Formatierungen stehen.');
            }
            if (! preg_match('~^\[(transfer|action|poll|scene)[ \t]*/\]$~i', $token, $marker)) {
                throw new InvalidArgumentException('Bitte verwende nur [transfer /], [poll /] oder [scene /] ohne zusätzliche Angaben.');
            }
            if ($start > $position) {
                $segments[] = ['text' => substr($text, $position, $start - $position)];
            }
            $segments[] = ['marker' => strtolower($marker[1])];
            $position = $end;
        }
        if ($position < strlen($text)) {
            $segments[] = ['text' => substr($text, $position)];
        }

        return $segments;
    }

    private function markupRanges(DOMNode $node, int &$offset, array &$ranges): void
    {
        $start = $offset;
        if ($node instanceof DOMText) {
            $offset += strlen($node->nodeValue);
        }
        foreach ($node->childNodes as $child) {
            $this->markupRanges($child, $offset, $ranges);
        }
        // s9e keeps source syntax in s/e/i nodes and formatted content in uppercase tags.
        if ($node instanceof DOMElement && (ctype_upper($node->nodeName) || in_array($node->nodeName, ['s', 'e', 'i'], true))) {
            $ranges[] = [
                'start' => $start, 'end' => $offset,
                'literal' => in_array($node->nodeName, ['Q', 'QUOTE', 'CODE', 's', 'e', 'i'], true),
            ];
        }
    }
}
