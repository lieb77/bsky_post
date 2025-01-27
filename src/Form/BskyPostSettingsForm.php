<?php

declare(strict_types=1);

namespace Drupal\bsky_post\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Configure Bluesky Post settings for this site.
 */
final class BskyPostSettingsForm extends ConfigFormBase
{

    protected $types;
    
    /**
     * {@inheritdoc}
     */
    public function getFormId(): string
    {
        return 'bsky_post_bsky_post_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames(): array
    {
        return ['bsky_post.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {

        // Get current settings
        $config = $this->config('bsky_post.settings')->get('types');

        // Get node types
        $types =  \Drupal\node\Entity\NodeType::loadMultiple();
        $options =  array_keys($types);
        $this->types = $options;

        $form['message'] = [
          '#type' => 'item',
          '#markup' => $this->t("Select the content types that you want to display the \"Post to Bluesky\" tab on"),
        ];
     
        $form['types'] = [
        '#type' => 'select',
        '#title' => $this->t('Select content types'),
        '#options' => $options,
        '#multiple' => true,
        '#default_value' => array_keys($config),
        ];
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state): void
    {
   
        if (count($form_state->getValue('types')) < 1) {
            $form_state->setErrorByName(
                'types',
                $this->t('You must select at least one content type.'),
            );
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $types = $form_state->getValue('types');
        foreach ($types as $type) {
            $settings[$type] = $this->types[$type];
        }

        $this->config('bsky_post.settings')
            ->set('types', $settings)
            ->save();
        parent::submitForm($form, $form_state);
        \Drupal::service("router.builder")->rebuild();
    }

}
