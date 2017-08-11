<!-- START BLOCK : feed-->
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>{title}</title>
		<link>{link}</link>
		<description>{description}</description>
		<language>lv</language>
		<atom:link href="{self}" rel="self" type="application/rss+xml" />
		<!-- START BLOCK : feed-item-->
		<item>
			<title>{title}</title>
			<link>{link}</link>
			<guid>{link}</guid>
			<description>{description}</description>
			<pubDate>{date}</pubDate>
			<dc:creator>{creator}</dc:creator>
			<category><![CDATA[{category}]]></category>
		</item>
		<!-- END BLOCK : feed-item-->
	</channel>
</rss>
<!-- END BLOCK : feed-->

