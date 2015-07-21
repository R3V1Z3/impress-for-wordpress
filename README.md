# Impress for WordPress
A WordPress plugin for easily using impress.js with WordPress content to create presentations by using shortcodes.

# Example
This html could be used to represent impress.js content normally:
```html
<div id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1">
    <p>This is my content. I can add whatever content I want here.</p>
</div>
```

With this plugin, it could be done by a shortcode in the WordPress editor like so:
```html
[impresswp id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1"]
<p>This is my WordPress content. I can add whatever content I want here.</p>
[/impresswp]
```

# Why another impress.js plugin?
I couldn't find any that used this particular method, one that creates slides from the provided content. This method lets users very easily create slides by using the already adept visual editor in WordPress. Using just a single Post/Page or other post type, a user can create a robust presentation.

# How does it work?
The plugin is incredibly simple, it just loads the impress.js library and then converts the shortcodes to div content that impress.js will expect and proceeds to initiate impress.js (calling init() from impress.js).

That's it, nothing complicated.
