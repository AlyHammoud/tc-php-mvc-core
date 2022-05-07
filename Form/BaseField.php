<?php

namespace Alimvc\PhpMvc\Form;

use Alimvc\PhpMvc\Model;

abstract class BaseField
{
    public function __construct(
        public Model $model,
        public string $attribute
    )
    {

    }

    abstract public function renderInput(): string;

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return sprintf(
            '
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">%s</label>
                        %s
                            <div class="invalid-feedback">
                                %s
                            </div>
                    </div>',
            $this->model->getLabels($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute));
    }
}