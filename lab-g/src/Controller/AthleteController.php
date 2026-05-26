<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Athlete;
use App\Service\Router;
use App\Service\Templating;

class AthleteController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $athletes = Athlete::findAll();

        $html = $templating->render('athlete/index.html.php', [
            'athletes' => $athletes,
            'router' => $router,
        ]);

        return $html;
    }

    public function createAction(?array $requestPost, Templating $templating, Router $router): ?string
    {
        if ($requestPost) {
            $athlete = Athlete::fromArray($requestPost);
            // @todo missing validation
            $athlete->save();

            $path = $router->generatePath('athlete-index');
            $router->redirect($path);

            return null;
        } else {
            $athlete = new Athlete();
        }

        $html = $templating->render('athlete/create.html.php', [
            'athlete' => $athlete,
            'router' => $router,
        ]);

        return $html;
    }

    public function editAction(int $athleteId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $athlete = Athlete::find($athleteId);

        if (! $athlete) {
            throw new NotFoundException("Missing athlete with id $athleteId");
        }

        if ($requestPost) {
            $athlete->fill($requestPost);
            // @todo missing validation
            $athlete->save();

            $path = $router->generatePath('athlete-index');
            $router->redirect($path);

            return null;
        }

        $html = $templating->render('athlete/edit.html.php', [
            'athlete' => $athlete,
            'router' => $router,
        ]);

        return $html;
    }

    public function showAction(int $athleteId, Templating $templating, Router $router): ?string
    {
        $athlete = Athlete::find($athleteId);

        if (! $athlete) {
            throw new NotFoundException("Missing athlete with id $athleteId");
        }

        $html = $templating->render('athlete/show.html.php', [
            'athlete' => $athlete,
            'router' => $router,
        ]);

        return $html;
    }

    public function deleteAction(int $athleteId, Router $router): ?string
    {
        $athlete = Athlete::find($athleteId);

        if (! $athlete) {
            throw new NotFoundException("Missing athlete with id $athleteId");
        }

        $athlete->delete();

        $path = $router->generatePath('athlete-index');
        $router->redirect($path);

        return null;
    }
}
