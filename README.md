# Simple Posts Module for [Hogan](https://github.com/dekodeinteraktiv/hogan-simple-posts)

## Installation
Install the module using Composer `composer require dekodeinteraktiv/hogan-simple-posts` or simply by downloading this repository and placing it in `wp-content/plugins`

## Usage
…

## Available filters
### Admin
- `hogan/module/simple_posts/post_type_link` : Filter hook for custom post type post link. Default wp post link.
- `hogan/module/simple_posts/the_title` : Filter hook for custom title. Default wp post title.
- `hogan/module/simple_posts/the_excerpt` : Filter hook for custom excerpt. Default wp post excerpt.
- `hogan/module/simple_posts/the_image_metadata_value` : Filter for returning a custom attachment id. Default null, which will fetch the post meta data for _thumbnail_id 
- `hogan/module/simple_posts/relationship/post_types` : Which post types to allow. Default `['post', 'page']`.
- `hogan/module/simple_posts/manual_list/max_count` : Max count for posts in manual list. Default `''` (infinite).
- `hogan/module/simple_posts/automatic_list/max_count` : Max count for posts in automatic list. Default `''` (infinite).

### Template

#### Filters
- `hogan/module/simple_posts/show_image_column` : Whether or not to show image column for a card in frontend. `false` if the card type is set to small, otherwise `true`
- `hogan/module/simple_posts/image_size` : Image thumb size for the card. Default `post-thumbnail``

#### Actions
- `hogan/module/simple_posts/after_the_excerpt` : Action to insert content after the excerpt.
