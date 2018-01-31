# Simple Posts Module for [Hogan](https://github.com/dekodeinteraktiv/hogan-simple-posts)

## Installation
Install the module using Composer `composer require dekodeinteraktiv/hogan-simple-posts` or simply by downloading this repository and placing it in `wp-content/plugins`

## Usage
…

## Available filters
### Admin
`hogan/module/simple_post/the_excerpt` : Filter hook for custom excerpt. Defaults wp excerpt.
`hogan/module/simple_post/manual_list/max_count` : Max count for posts in manual list. Default `''` (infinite).
`hogan/module/simple_post/automatic_list/max_count` : Max count for posts in automatic list. Default `''` (infinite).

### Template
`hogan/module/simple_post/show_image_column` : Whether or not to show image column for a card in frontend. `false` if the card type is set to small, otherwise `true`
`hogan/module/simple_posts/image_size` : Image thumb size for the card. Default `post-thumbnail``
