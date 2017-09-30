<?php

namespace ForumBundle\Form\Type\Admin\Board;

use CCDNForum\ForumBundle\Form\Type\Admin\Board\BoardUpdateFormType as BaseBoardUpdateFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class BoardUpdateFormType extends BaseBoardUpdateFormType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity',
                array(
                    'property'           => 'name',
                    'class'              => $this->categoryClass,
                    'group_by'           => 'forum.name',
                    'query_builder'      =>
                        function (EntityRepository $er) {
                            return $er
                                ->createQueryBuilder('c')
                                ->leftJoin('c.forum', 'f')
                            ;
                        },
                    'required'           => false,
                    'label'              => 'category.label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
            ->add('name', 'text',
                array(
                    'label'              => 'board.name-label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
            ->add('description', 'textarea',
                array(
                    'label'              => 'board.description-label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
            ->add('readAuthorisedRoles', 'choice',
                array(
                    'required'           => false,
                    'expanded'           => true,
                    'multiple'           => true,
                    'choices'            => $options['available_roles'],
                    'label'              => 'board.roles.topic-view-label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
            ->add('topicCreateAuthorisedRoles', 'choice',
                array(
                    'required'           => false,
                    'expanded'           => true,
                    'multiple'           => true,
                    'choices'            => $options['available_roles'],
                    'label'              => 'board.roles.topic-create-label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
            ->add('topicReplyAuthorisedRoles', 'choice',
                array(
                    'required'           => false,
                    'expanded'           => true,
                    'multiple'           => true,
                    'choices'            => $options['available_roles'],
                    'label'              => 'board.roles.topic-reply-label',
                    'translation_domain' => 'CCDNForumForumBundle',
                )
            )
        ;
    }

}
