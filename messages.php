<?php
/**
 * Plugin Name:     messages
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     this is for events crud
 * Author:          omarenis
 * Author URI:      YOUR SITE HERE
 * Text Domain:     messages
 * Domain Path:     /messages
 * Version:         0.1.0
 * @package         Messages
 */

/**
 * @throws Exception
 */




class MessagePlugin
{
    public string $name;
    private bool $successInsert;
    private bool $successSend;
    private string $message;

    public function __construct()
    {
        $this->name = plugin_basename(__FILE__);
        $this->message = '';
    }
    public function activate()
    {
        flush_rewrite_rules();
    }

    public function deactivate()
    {
        flush_rewrite_rules();
    }

    public function createCustomPost()
    {
        $supports = array(
            'title', // post title
            'editor', // post content
        );
        $labels = array(
            'name' => _x('messages', 'plural'),
            'singular_name' => _x('Message', 'singular'),
            'menu_name' => _x('Message', 'admin menu'),
            'name_admin_bar' => _x('messages', 'admin bar'),
            'add_new' => _x('Add New', 'add new event'),
            'add_new_item' => __('Add New message'),
            'new_item' => __('New message'),
            'edit_item' => __('Edit message'),
            'view_item' => __('View message'),
            'all_items' => __('All messages'),
            'search_items' => __('Search messages'),
            'not_found' => __('No message found.'),
        );
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'Message'),
            'has_archive' => true,
            'hierarchical' => false,
        );
        register_post_type('Message', $args);
    }
    public function notifyAdmin()
    {
        $noticeClass = $this->successSend && $this->successInsert ? 'notice-success' :
            ($this->successInsert ? 'notice-warning' : 'notice-error');
        print_r("<div class='$noticeClass is-dismissible'>$this->message</div>");
    }

    public function create($post_id, $post)
    {
        $postFound = get_posts(['title' => $post->post_title]);

        if ($postFound != null) {
            $this->message = 'l y a un article avec le même titre ue le message';
            add_action('admin_notices', array($this, 'notifyAdmin'));
        } else {
            $postId = wp_insert_post([
                'title' => $post->post_title,
                'post_type' => 'message', 'post_content' => $post->post_content
            ]);
            if (gettype($postId) == 'int') {
                $this->message = 'le message est inseré';
                $this->successInsert = true;
                $to = get_option('admin_email');
                $subject = $post->post_title;
                $message = $post->post_content;
                $successful = wp_mail($to, $subject, $message);
                $this->successSend = $successful;
                if ($successful) {
                    $this->message  .= "et envoyé vers l'admin";
                } else {
                    $this->message .= "mais l'envoie vers l'admin à échoué";
                }
            } else {
                $this->successInsert = false;
                $this->message = "n'est pas inséré proprement";
            }
        }
        add_action('admin_notices', array($this, 'notifyAdmin'));
    }
    final public static function uninstall()
    {
        $messages = get_posts(array('post_type' => 'Message', 'numberposts' => -1));
        foreach ($messages as $event) {
            wp_delete_post($event->ID, true);
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
$messagePlugin = new MessagePlugin();
add_action('init', array($messagePlugin, 'createCustomPost'));
add_action('save_post', array($messagePlugin, 'create'));
