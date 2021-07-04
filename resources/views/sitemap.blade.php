<?php

use App\Models\Chapter;
use App\Models\Comic;

$filename = public_path() . '/storage/sitemap.xml';
if (file_exists($filename) && time() - filemtime($filename) < 60*60) {
    $file = fopen($filename, 'r') or die("Unable to load the sitemap");
    echo fread($file, filesize($filename));
    fclose($file);
    header('Cached: true');
    return;
}
$base_url = request()->root();
$comics = Comic::published()->get();
$last_chapter = Chapter::published()->select('published_on')->max('published_on');
$lastmod = $last_chapter ? date(DATE_ATOM, strtotime($last_chapter)) : date(DATE_ATOM, time());
ob_start();
?>
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="{{asset('/storage/default.xsl')}}"?>
<urlset
    xmlns="https://www.sitemaps.org/schemas/sitemap/0.9/"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:image="https://www.google.com/schemas/sitemap-image/1.1"
>
    <url>
        <loc><![CDATA[{{$base_url}}]]></loc>
        <lastmod><![CDATA[{{$lastmod}}]]></lastmod>
        <changefreq><![CDATA[always]]></changefreq>
        <priority><![CDATA[1.0]]></priority>
    </url>
    <url>
        <loc><![CDATA[{{$base_url}}]]></loc>
        <lastmod><![CDATA[{{$lastmod}}]]></lastmod>
        <changefreq><![CDATA[always]]></changefreq>
        <priority><![CDATA[1.0]]></priority>
    </url>
    <url>
        <loc><![CDATA[{{$base_url}}/comics]]></loc>
        <lastmod><![CDATA[{{$lastmod}}]]></lastmod>
        <changefreq><![CDATA[always]]></changefreq>
        <priority><![CDATA[0.9]]></priority>
    </url>
    <url>
        <loc><![CDATA[{{$base_url}}/alph]]></loc>
        <lastmod><![CDATA[{{$lastmod}}]]></lastmod>
        <changefreq><![CDATA[always]]></changefreq>
        <priority><![CDATA[0.9]]></priority>
    </url>
    <url>
        <loc><![CDATA[{{$base_url}}/last]]></loc>
        <lastmod><![CDATA[{{$lastmod}}]]></lastmod>
        <changefreq><![CDATA[always]]></changefreq>
        <priority><![CDATA[0.9]]></priority>
    </url>
@foreach($comics as $comic)
<?php $last_published = $comic->created_at; ?>
@foreach($comic->publishedChapters()->get() as $chapter)
    <url>
        <loc><![CDATA[{{$base_url . Chapter::getUrl($comic, $chapter)}}]]></loc>
        <lastmod><![CDATA[{{$chapter->published_on->format('c')}}]]></lastmod>
        <changefreq><![CDATA[weekly]]></changefreq>
        <priority><![CDATA[0.3]]></priority>
    </url>
<?php if($chapter->published_on > $last_published) $last_published = $chapter->published_on; ?>
@endforeach
    <url>
        <loc><![CDATA[{{$base_url . Comic::getUrl($comic)}}]]></loc>
        <lastmod><![CDATA[{{$last_published->format('c')}}]]></lastmod>
        <changefreq><![CDATA[daily]]></changefreq>
        <priority><![CDATA[0.7]]></priority>
        <image:image>
            <image:loc><![CDATA[{{Comic::getThumbnailUrl($comic)}}]]></image:loc>
            <image:title><![CDATA[{{$comic->name}}]]></image:title>
        </image:image>
    </url>
@endforeach
</urlset>
<?php
$xmlStr = ob_get_contents();
ob_end_clean();
file_put_contents($filename, $xmlStr);
echo $xmlStr;
?>
