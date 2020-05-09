<?php

declare(strict_types=1);

namespace AskNicely\BPro;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

class AbstractCommand extends Command
{
    /** @var Serializer $serializer */
    protected $serializer;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->serializer = new Serializer([new ArrayDenormalizer()], [new CsvEncoder(), new JsonEncoder()]);
    }
}
