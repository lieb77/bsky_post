<?php

declare(strict_types=1);

namespace Drupal\bsky_post\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bsky_post\BskyPost;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Provides a form for posting to Bluesky.
 */
final class BskyPostForm extends FormBase {

  /**
   * Instance of the BskyPost service.
   *
   * @var Drupal\bsky_post\BskyPost
   */
  protected $bskyService;

  /**
   * Node id.
   *
   * @var int
   */
  protected $nid;

  /**
   * Contains a post.
   *
   * @var array
   */
  protected $post;

  /**
   * Instantiate our form class and load the services we need.
   *
   * @param \Drupal\bsky_post\BskyPost $bskyService
   *   The post service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match interface.
   */
  public function __construct(
    BskyPost $bskyService,
    RouteMatchInterface $routeMatch,
  ) {
    $this->bskyService = $bskyService;

    $node = $routeMatch->getParameter('node');
    if (!empty($node)) {
      // Save nid the route back.
      $this->nid = $node->id();

      // Get the title.
      $title = $node->getTitle();
      // Get the body summary.
      $text = $node->get('body')->summary;      
      if (empty($text)){
      	$text = text_summary($node->get('body')->value, 1, 300);
      }
      // Get the link.
      $link = $node->toUrl()->setAbsolute()->toString();

      $this->post = [
        'title' => $title,
        'text'  => $text,
        'link'  => $link,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
          $container->get('bsky_post.bsky_post'),
          $container->get('current_route_match')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsky_post_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    if (!empty($this->post)) {

      $form['title'] = [
        '#type'     => 'textfield',
        '#title' => $this->t("Post title"),
        '#default_value' => $this->post['title'],
      ];

			$help_text = $this->t("If you have not provided a summary, the first 300 characters of the body will appear here. "
														. "You can edit this anyway you like as this is the text you will be posting.");

      $form['text'] = [
        '#type'      => 'textarea',
        '#title' => $this->t("Post summary"),
        '#description' => $help_text,
        '#description_display' => 'after',
        '#default_value' => $this->post['text'],
      ];

      $form['link'] = [
        '#type'     => 'textfield',
        '#title' => $this->t("Post link"),
        '#default_value' => $this->post['link'],
      ];

      $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
          '#type' => 'submit',
          '#value' => $this->t('Post this to Bluesky!'),
        ],
      ];
    }
    else {
      $form = [
        '#type' => 'item',
        '#markup' => $this->t("This should never happen."),
      ];

    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {

    // Check the length of the post.
    $message = $form_state->getValue('title') . "\n" .
               $form_state->getValue('text');

    if (mb_strlen($message) > 300) {
      $form_state->setErrorByName(
            'text',
            $this->t('Message is too long for Bluesky. Must be lass than 300 things.'),
        );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Share post to Bluesky.
    $message = $form_state->getValue('title') . "\n" .
          $form_state->getValue('text');

    $link = $form_state->getValue('link');

    $err = $this->bskyService->post($message, $link);

    if (FALSE === $err) {
      $this->messenger()->addStatus($this->t("The post has been shared."));
      $form_state->setRedirect('entity.node.canonical', ['node' => $this->nid]);
    }
    else {
      $this->messenger()->addStatus($err);
      $form_state->setRebuild();

    }
  }

  // End of class.
}
