# Laravel Validation Service

[![Total Downloads](https://poser.pugx.org/prettus/laravel-validation/downloads.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![Latest Stable Version](https://poser.pugx.org/prettus/laravel-validation/v/stable.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![Latest Unstable Version](https://poser.pugx.org/prettus/laravel-validation/v/unstable.svg)](https://packagist.org/packages/prettus/laravel-validation)
[![License](https://poser.pugx.org/prettus/laravel-validation/license.svg)](https://packagist.org/packages/prettus/laravel-validation)

## Instalação

Edite o seu arquivo composer.json e adicione "prettus/laravel-repository": "dev-master" nas dependencias.
 
    "prettus/laravel-validation": "dev-master"
    
### Criar um Validator

Um Validator pode conter regras de validação para criação e edição do seu model.

    Prettus\Validator\Contracts\ValidatorInterface::RULE_CREATE
    Prettus\Validator\Contracts\ValidatorInterface::RULE_UPDATE
    
O exemplo abaixo ira utilizar as regras que foram definidas tanto na criação, como na edição do model

    <?php
    
    use \Prettus\Validator\LaravelValidator;
    
    class PostValidator extends LaravelValidator {
    
        protected $rules = array(
            'title' => 'required',
            'text'  => 'min:3',
            'author'=> 'required'
        );
    
    }

Para definir regras especificas, use :

    <?php
    
    use \Prettus\Validator\LaravelValidator;
    
    class PostValidator extends LaravelValidator {
    
        protected $rules = array(
            ValidatorInterface::RULE_CREATE=>array(
                'title' => 'required',
                'text'  => 'min:3',
                'author'=> 'required'
            ),
            ValidatorInterface::RULE_UPDATE=>array(
                'title' => 'required'
            )
        )
    
    }
    
### Usando o Validator

<?php
    
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
    
                return Response::json(array(
                    'message'=>'Post created',
                    'data'   =>$post->toArray()
                ));
    
            } catch (ValidatorException $e) {
    
                return Response::json(array(
                    'error'   =>true,
                    'message' =>$e->getMessage()
                ));
    
            }
        }
    
        public function update($id)
        {
    
            try{
                
                $this->validator->with( Input::all() )->passesOrFail( ValidatorInterface::RULE_UPDATE );
                
                $post = $this->repository->update( Input::all(), $id );
    
                return Response::json(array(
                    'message'=>'Post created',
                    'data'   =>$post->toArray()
                ));
    
            }catch (ValidatorException $e){
    
                return Response::json(array(
                    'error'   =>true,
                    'message' =>$e->getMessage()
                ));
    
            }
    
        }
    }
    

# Autor

Anderson Andrade - <contato@andersonandra.de>

## Credits

http://culttt.com/2014/01/13/advanced-validation-service-laravel-4/