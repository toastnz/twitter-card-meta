<meta name="twitter:card" content="<% if $TwitterCardType %>$TwitterCardType<% else %>summary_large_image<% end_if %>">
<meta name="twitter:site" content="{$TwitterSite}">
<% if $CreatorHandle %>
    <meta name="twitter:creator" content="{$CreatorHandle}">
<% else %>
    <meta name="twitter:creator" content="{$TwitterSite}">
<% end_if %>
<meta name="twitter:title" content="{$TwitterTitle}">
<meta name="twitter:description" content="{$TwitterDescription}">
<meta name="twitter:image" content="{$TwitterImageURL}">
