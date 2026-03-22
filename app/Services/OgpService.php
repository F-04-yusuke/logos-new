<?php

namespace App\Services;

class OgpService
{
    /**
     * 指定 URL の OGP 情報（タイトル・サムネイル）を取得する。
     *
     * @return array{title: string|null, thumbnail_url: string|null}
     */
    public static function fetch(string $url): array
    {
        $title         = null;
        $thumbnail_url = null;

        try {
            $context = stream_context_create([
                'http' => [
                    'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                    'timeout' => 5,
                ],
            ]);
            $html = @file_get_contents($url, false, $context);
            if ($html) {
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                    $title = html_entity_decode($m[1]);
                }
                if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $m)) {
                    $title = html_entity_decode($m[1]);
                }
                if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $m)) {
                    $thumbnail_url = mb_substr(html_entity_decode($m[1]), 0, 2048);
                }
            }
        } catch (\Exception $e) {}

        return compact('title', 'thumbnail_url');
    }
}
