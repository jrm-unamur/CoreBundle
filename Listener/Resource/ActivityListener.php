<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Listener\Resource;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Entity\Resource\Activity;
use Claroline\CoreBundle\Entity\Resource\ResourceActivity;
use Claroline\CoreBundle\Form\ActivityType;
use Claroline\CoreBundle\Event\CopyResourceEvent;
use Claroline\CoreBundle\Event\CreateFormResourceEvent;
use Claroline\CoreBundle\Event\CreateResourceEvent;
use Claroline\CoreBundle\Event\OpenResourceEvent;
use Claroline\CoreBundle\Event\CustomActionResourceEvent;
use Claroline\CoreBundle\Event\ExportResourceTemplateEvent;
use Claroline\CoreBundle\Event\ImportResourceTemplateEvent;
use Claroline\CoreBundle\Event\DeleteResourceEvent;

/**
 * @DI\Service
 */
class ActivityListener implements ContainerAwareInterface
{
    private $container;

    /**
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @DI\Observe("create_form_activity")
     *
     * @param CreateFormResourceEvent $event
     */
    public function onCreateForm(CreateFormResourceEvent $event)
    {
        $form = $this->container->get('form.factory')->create(new ActivityType, new Activity());
        $content = $this->container->get('templating')->render(
            'ClarolineCoreBundle:Resource:createForm.html.twig',
            array(
                'form' => $form->createView(),
                'resourceType' => 'activity'
            )
        );
        $event->setResponseContent($content);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("create_activity")
     *
     * @param CreateResourceEvent $event
     */
    public function onCreate(CreateResourceEvent $event)
    {
        $request = $this->container->get('request');
        $form = $this->container
            ->get('form.factory')
            ->create(new ActivityType(), new Activity());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $event->setResources(array($form->getData()));
            $event->stopPropagation();

            return;
        }

        $content = $this->container->get('templating')->render(
            'ClarolineCoreBundle:Resource:createForm.html.twig',
            array(
                'form' => $form->createView(),
                'resourceType' => 'activity'
            )
        );
        $event->setErrorFormContent($content);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("delete_activity")
     *
     * @param DeleteResourceEvent $event
     */
    public function onDelete(DeleteResourceEvent $event)
    {
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("copy_activity")
     *
     * @todo: Do the resources need to be copied ?
     *
     * @param CopyResourceEvent $event
     */
    public function onCopy(CopyResourceEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $resource = $event->getResource();
        $copy = new Activity();
        $copy->setInstructions($resource->getInstructions());
        $resourceActivities = $resource->getResourceActivities();

        foreach ($resourceActivities as $resourceActivity) {
            $ra = new ResourceActivity();
            $ra->setResourceNode($resourceActivity->getResourceNode());
            $ra->setSequenceOrder($resourceActivity->getSequenceOrder());
            $ra->setActivity($copy);
            $em->persist($ra);
        }

        $em->persist($copy);
        $event->setCopy($copy);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("resource_activity_to_template")
     *
     * @param ExportResourceTemplateEvent $event
     */
    public function onExportTemplate(ExportResourceTemplateEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $resource = $event->getResource();
        $config['instructions'] = $resource->getInstructions();
        $resourceActivities = $em->getRepository('ClarolineCoreBundle:Resource\ResourceActivity')
            ->findResourceActivities($resource);
        $resourceDependencies = array();

        foreach ($resourceActivities as $resourceActivity) {
            if ($resourceActivity->getResourceNode()->getWorkspace() ===
                $resource->getResourceNode()->getWorkspace()) {
                $resourceActivityConfig['id'] = $resourceActivity->getResourceNode()->getId();
                $resourceActivityConfig['order'] = $resourceActivity->getSequenceOrder();
                $config['resources'][] = $resourceActivityConfig;
            }
        }

        $event->setFiles($resourceDependencies);
        $event->setConfig($config);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("resource_activity_from_template")
     *
     * @param ImportResourceTemplateEvent $event
     */
    public function onImportTemplate(ImportResourceTemplateEvent $event)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $config = $event->getConfig();
        $activity = new Activity();
        $activity->setInstructions($config['instructions']);

        foreach ($config['resources'] as $data) {
            $resourceActivity = new ResourceActivity();
            $resourceActivity->setResource($event->find($data['id']));
            $resourceActivity->setSequenceOrder($data['order']);
            $resourceActivity->setActivity($activity);
            $em->persist($resourceActivity);
        }

        $event->setResource($activity);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("open_activity")
     *
     * @param OpenResourceEvent $event
     */
    public function onOpen(OpenResourceEvent $event)
    {
        $activity = $event->getResource();
        $content = $this->container->get('templating')->render(
            'ClarolineCoreBundle:Activity/player:activity.html.twig',
            array('activity' => $activity)
        );

        $response = new Response($content);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    /**
     * @DI\Observe("compose_activity")
     */
    public function onCompose(CustomActionResourceEvent $event)
    {
        $resourceTypes = $this->container->get('doctrine.orm.entity_manager')
            ->getRepository('ClarolineCoreBundle:Resource\ResourceType')
            ->findAll();
        $activity = $event->getResource();
        $resourceActivities = $this->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('ClarolineCoreBundle:Resource\ResourceActivity')
            ->findResourceActivities($activity);

        $content = $this->container->get('templating')->render(
            'ClarolineCoreBundle:Activity:index.html.twig',
            array(
                'resourceTypes' => $resourceTypes,
                'resourceActivities' => $resourceActivities,
                '_resource' => $activity
            )
        );

        $response = new Response($content);
        $event->setResponse($response);
        $event->stopPropagation();
    }
}
