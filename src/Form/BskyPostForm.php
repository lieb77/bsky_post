<?php

declare(strict_types=1);

namespace Drupal\bsky_post\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\lbsky_postBskyPost;

/**
 * Provides a Liebslog form.
 */
final class BskyPostForm extends FormBase
{
    protected $bsky_service;
    protected $post;

    /* 
    * Instantiate our form class 
    * and load the services we need
    *
    */
    public function __construct(BskyPost $bsky_service ) {        
        $this->bsky_service = $bsky_service;
        
        $node = \Drupal::routeMatch()->getParameter('node');
        if (!empty($node) ) {
            // Get the title
            $title = $node->getTitle();
            // Get the body summary
            $text  = $node->get('body')->summary;
            // Get the link        
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
    public static function create(ContainerInterface $container)
    {
        // Instantiates this form class.
        return new static(
            $container->get('bsky_post.bsky_post'),
        );
    }

    /*
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'bsky_post_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
             
        if (!empty($this->post) {
        
            $form['title'] = [
            '#type'     => 'textfield',
            '#title' => $this->t("Post title"),
            '#default_value' => $this->t($this->post['title']),
            ];
            
            $form['text'] =[
            '#type'      => 'textarea',
            '#title' => $this->t("Post summary"),
            '#default_value' => $this->t($this->post['text']),
            ];
            
            $form['link'] = [
            '#type'     => 'textfield',
            '#title' => $this->t("Post link"),
            '#default_value' => $this->t($this->post['link']),
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
            ]
          
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state): void
    {
    
        // Check the length of the post
        $message = $form_state->getValue('title') . "\n" .
               $form_state->getValue('text');
         
        if (mb_strlen($message) > 300) {
            $form_state->setErrorByName('text',
                $this->t('Message is too long for Bluesky. Must be lass than 300 things.'),
            );
        } 
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {               
        // Share post to Bluesky        
        
        $message = $form_state->getValue('title') . "\n" .
          $form_state->getValue('text');
        
        $link = $form_state->getValue('link');
        
        $err = $this->bsky_service->post($message, $link);
         
        if (false === $err) {        
            $this->messenger()->addStatus($this->t("The post has been shared."));
            $form_state->setRedirect([front]);                         
        }            
        else {
            $this->messenger()->addStatus($this->t($err));
            $form_state->setRebuild();

        }
    }


} // End of class
