<?php
/**
 * Plugin Name:     messages_plugin
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          omarenis
 * Author URI:      YOUR SITE HERE
 * Text Domain:     admin_notif
 * Domain Path:     /messages
 * Version:         0.1.0
 *
 * @package         messages
 */

// Your code starts here.
class AdminNotifyPlugin
{
    private string $name;
    /**
     * @var false
     */
    private bool $successInsert;
    /**
     * @var false
     */
    private bool $successSend;
    private string $message;

    public function __construct()
    {
        $this->name = plugin_basename(__FILE__);
        $this->successInsert = false;
        $this->message = '';
        $this->successSend = false;
    }

    public function createCustomPost()
    {
        $supports = array(
            'title', // post title
            'editor', // post content
            'author', // post author
            'thumbnail', // featured images
            'excerpt', // post excerpt
            'custom-fields', // custom fields
            'comments', // post comments
            'revisions', // post revisions
            'post-formats', // post formats
        );
        $labels = array(
            'name' => _x('messages', 'plural'),
            'singular_name' => _x('message', 'singular'),
            'menu_name' => _x('message', 'admin menu'),
            'name_admin_bar' => _x('messages', 'admin bar'),
            'add_new' => _x('Add New message', 'add new Message'),
            'add_new_item' => __('Add New Message'),
            'new_item' => __('New Message'),
            'edit_item' => __('Edit Message'),
            'view_item' => __('View Message'),
            'all_items' => __('All Message'),
            'search_items' => __('Search Message'),
            'not_found' => __('No Messages found.'),
        );
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'messages'),
            'has_archive' => true,
            'hierarchical' => false,
        );
        register_post_type('message', $args);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function notifyAdmin()
    {
        $noticeClass = $this->successInsert && $this->successSend ? 'notice-success' :
            ($this->successInsert ? 'notice-warning' : 'notice-error');
        print_r("<div class='$noticeClass is-dismissible'>$this->message</div>");
    }

    public function create($post_id, $post)
    {
        $postFound = get_posts(['title' => $post->post_title]);
        if ($postFound != null) {
            $this->message = 'il existe déjâ un article avec le même titre';
        } else {
            $postId = wp_insert_post([
                'title' => $post->post_title,
                'post_type' => 'message', 'post_content' => $post->post_content
            ]);
            $this->successInsert = gettype($postId) == 'int';
            if ($this->successInsert) {
                $this->message = 'votre message a été bien enregistré';
                $to = get_option('admin_email');
                $subject = $post->post_title;
                $message = $post->post_content;
                $this->successSend = wp_mail($to, $subject, $message);
                $this->message .= ($this->successSend ? ' et envoyé' : ' mais pas envoyé') . 'à l\'admin';
            }
            add_action('admin_notices', array($this, 'notifyAdmin'));
        }
    }

    public function activate()
    {
        add_action('init', array($this, 'createCustomPost'));
        add_action('save_post', array($this, 'create'));
        flush_rewrite_rules();
    }
}

try {
    $adminNotifyPlugin = new AdminNotifyPlugin();
    register_activation_hook(__FILE__, array($adminNotifyPlugin, 'activate'));
    register_deactivation_hook(__FILE__, array($adminNotifyPlugin, 'deactivate'));
} catch (Exception $e) {
    echo $e->getMessage();
}
