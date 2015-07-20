# Impress for WordPress
A WordPress plugin for easily using impress.js with WordPress content to create presentations by using shortcodes.

# An example
For example, this html could be used to represent impress.js content:
```html
<div id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1">
    <p>This is my content. I can add whatever content I want here.</p>
</div>
```

With this plugin, it could be done by a shortcode in the WordPress editor like so:
```html
[impress-shortcode id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1"]
<p>This is my WordPress content. I can add whatever content I want here.</p>
[/impress-shortcode]
```