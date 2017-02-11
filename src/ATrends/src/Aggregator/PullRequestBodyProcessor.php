<?php

namespace Aa\ATrends\Aggregator;

class PullRequestBodyProcessor
{
    /**
     * @param string $body
     *
     * @return array
     */
    public function getIssueNumbers($body)
    {
        $patterns = [
            '/Fixes the following tickets:\s?([0-9#,\s]*)\s?\R/',
            '/Fixes the following tickets:\s?([0-9#,\s]*)$/',
            '/\| Fixed tickets\s\|\s?([0-9#,\h]*)?/',
        ];

        $matches = [];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $body, $matches)) {

                $text = $matches[1];

                return $this->getIssueNumbersFromText($text);
            }
        }

        return [];
    }

    /**
     * @param string $text
     *
     * @return array
     */
    private function getIssueNumbersFromText($text)
    {
        if (empty($text)) {
            return [];
        }

        $issueNumbers = explode(',', $text);

        return array_map(function ($item) {
            return (int)str_replace('#', '', trim($item));
        }, $issueNumbers);
    }
}
