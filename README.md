# Impress for WordPress
A WordPress plugin for easily using impress.js with WordPress content to create presentations by using shortcodes.

# Example
This html could be used to represent impress.js content normally:
```html
<div id="impress">
<div id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1">
    <p>This is my content. I can add whatever content I want here.</p>
</div>
</div>
```

This plugin works the same way but with a shortcode for the impress div:
```html
[impresswp]
<div id="slide1" class="step" data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1">
    <p>This is my content. I can add whatever content I want here.</p>
</div>
[/impresswp]
```

Or you can use step shortcodes like so:
```html
[impresswp]
[imstep data-x="2825" data-y="2325" data-z="-3000" data-rotate="300" data-scale="1"]
<p>This is my WordPress content. I can add whatever content I want here.</p>
[/imstep]
[/impresswp]
```

There are two benefits of using the [imstep] shortcodes, the first being that you won't need to switch to the Text mode in the visual editor to add them (divs can otherwise only feasibly be added through Text mode).

The main benefit though, it can auto-increment the ids so you don't need to create unique ids yourself. Though you can always specify an id attribute if you want to use a custom id still.

# Why another impress.js plugin?
I couldn't find any that used this particular method, one that creates slides from the provided content. This method lets users very easily create slides by using the already adept visual editor in WordPress. Using just a single Post/Page or other post type, a user can create a robust presentation.

# How does it work?
The plugin is incredibly simple, it just loads the impress.js library and then converts the shortcodes to div content that impress.js will expect and proceeds to initiate impress.js (calling init() from impress.js).

That's it, nothing complicated.