# Laravel Validation Service

[![Total Downloads](https://poser.pugx.org/prettus/laravel-validation/downloads.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![Latest Stable Version](https://poser.pugx.org/prettus/laravel-validation/v/stable.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![Latest Unstable Version](https://poser.pugx.org/prettus/laravel-validation/v/unstable.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![License](https://poser.pugx.org/prettus/laravel-validation/license.svg)](https://packagist.org/packages/prettus/laravel-validation)

## Installation

Add "prettus/laravel-repository": "1.1.*" to composer.json
 
```json
"prettus/laravel-validation": "1.1.*"
```
    
### Create a validator

The Validator contains rules for adding, editing.

```php
Prettus\Validator\Contracts\ValidatorInterface::RULE_CREATE
Prettus\Validator\Contracts\ValidatorInterface::RULE_UPDATE
```
    
In the example below, we define some rules for both creation and edition

```php
use \Prettus\Validator\LaravelValidator;

class PostValidator extends LaravelValidator {

    protected $rules = [
        'title' => 'required',
        'text'  => 'min:3',
        'author'=> 'required'
    ];

}

```

To define specific rules, proceed as shown below:

```php

use \Prettus\Validator\LaravelValidator;

class PostValidator extends LaravelValidator {

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title' => 'required',
            'text'  => 'min:3',
            'author'=> 'required'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'title' => 'required'
        ]
   ];

}

```

### Custom Error Messages

You may use custom error messages for validation instead of the defaults

```php

protected $messages = [
    'required' => 'The :attribute field is required.',
];

```

Or, you may wish to specify a custom error messages only for a specific field.

```php

protected $messages = [
    'email.required' => 'We need to know your e-mail address!',
];

```

### Custom Attributes

You too may use custom name attributes

```php

protected $attributes = [
    'email' => 'E-mail',
    'obs' => 'Observation',
];

```

### Using the Validator

```php

use \Prettus\Validator\Exceptions\ValidatorException;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;
    
    /**
     * @var PostValidator
     */
    protected $validator;

    public function __construct(PostRepository $repository, PostValidator $validator){
        $this->repository = $repository;
        $this->validator  = $validator;
    }
   
    public function store()
    {

        try {

            $this->validator->with( Input::all() )->passesOrFail();
            
            // OR $this->validator->with( Input::all() )->passesOrFail( ValidatorInterface::RULE_CREATE );

            $post = $this->repository->create( Input::all() );

            return Response::json([
                'message'=>'Post created',
                'data'   =>$post->toArray()
            ]);

        } catch (ValidatorException $e) {

            return Response::json([
                'error'   =>true,
                'message' =>$e->getMessage()
            ]);

        }
    }

    public function update($id)
    {

        try{
            
            $this->validator->with( Input::all() )->passesOrFail( ValidatorInterface::RULE_UPDATE );
            
            $post = $this->repository->update( Input::all(), $id );

            return Response::json([
                'message'=>'Post created',
                'data'   =>$post->toArray()
            ]);

        }catch (ValidatorException $e){

            return Response::json([
                'error'   =>true,
                'message' =>$e->getMessage()
            ]);

        }

    }
}
```

# Author

Anderson Andrade - <contato@andersonandra.de>

## Credits

http://culttt.com/2014/01/13/advanced-validation-service-laravel-4/
