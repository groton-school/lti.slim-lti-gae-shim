<?php

declare(strict_types=1);

namespace GrotonSchool\Slim\LTI\GAE\Infrastructure\Firestore;

interface FirestoreObject
{
    public function name(): string;
}
