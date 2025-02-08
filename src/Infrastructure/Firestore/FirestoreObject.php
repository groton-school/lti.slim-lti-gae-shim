<?php

declare(strict_types=1);

namespace GrotonSchool\SlimLTI\GAE\Infrastructure\Firestore;

interface FirestoreObject
{
    public function name(): string;
}
