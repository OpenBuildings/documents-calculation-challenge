<?php

namespace Interfaces;

interface ValidatorInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function validate($data);
}
