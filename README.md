Lincode - REST API
==============================

Essa bundle é responsável para a criação de API's RESTFull

## Instalação

#### Passo 1: Adicionando no Composer

Para instalar REST API Bundle pelo Composer basta adicionar ao seu arquivo
`composer.json` o seguinte módulo:

```js
// composer.json
{
    // ...
    "repositories": [
          "type": "vcs",
          "url": "https://{USUÁRIO}:{SENHA}@gitlab.com/lincode/fly-rest.git"
        }
    ],
    // ...
    "require": {
        // ...
        "lincode/rest-api": "dev-master"
    }
}
```

**NOTA**: Utilizando `dev-master` sempre será instalada a versão mais recente do repositório.

Em seguida, você pode instalar as novas dependências executando o comando Composer `` update`` a partir do diretório onde o arquivo `` composer.json`` está localizado:

```bash
$ php composer.phar update
```
O Composer baixará automaticamente todos os arquivos e dependências necessárias e instalá-los para você.

#### Passo 2: Ativando Bundle

O próximo passo é atualizar o seu arquivo ``AppKernel.php`` e registrar os novos bundles para o funcionamento do Oauth2 e tambem o REST:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    $bundles[] = new Voryx\RESTGeneratorBundle\VoryxRESTGeneratorBundle();
    $bundles[] = new FOS\RestBundle\FOSRestBundle();
    $bundles[] = new FOS\OAuthServerBundle\FOSOAuthServerBundle();
    $bundles[] = new Nelmio\ApiDocBundle\NelmioApiDocBundle();
    $bundles[] = new JMS\SerializerBundle\JMSSerializerBundle($this);
    $bundles[] = new Lincode\RestApi\Bundle\RestApiBundle();
    // ...
);
```
**NOTA**: É recomendado registrar a nova bundle após ``CMSBundle`` do componente ``taginterativa/angus-cms``.

#### Passo 3: Configurar as Bundles

Será necessário adicionar algumas linhas de configuração para a aplicação. Para isso acesse os seguintes arquivos e insira as linhas abaixo:

# app/config/config.yml
``` yaml

doctrine:
    // ...
    orm:
        // ...
        resolve_target_entities:
            Lincode\RestApi\Bundle\Entity\User: '%oauth2_entity_mapping%'

fos_rest:
    routing_loader:
        default_format: json
    param_fetcher_listener: true
    body_listener: true
    disable_csrf_role: IS_AUTHENTICATED_FULLY
    body_converter:
        enabled: true
    view:
        view_response_listener: force

fos_oauth_server:
    db_driver: orm
    client_class:        Lincode\RestApi\Bundle\Entity\Client
    access_token_class:  Lincode\RestApi\Bundle\Entity\AccessToken
    refresh_token_class: Lincode\RestApi\Bundle\Entity\RefreshToken
    auth_code_class:     Lincode\RestApi\Bundle\Entity\AuthCode
    service:
        user_provider: platform.user.provider

nelmio_api_doc:
    name:                 'Application Name'
```

# app/config/routing.yml
``` yaml

// ...
api_routing:
    resource: "@RestApiBundle/Resources/config/routing.yml"
    prefix:   /

NelmioApiDocBundle:
    resource: "@NelmioApiDocBundle/Resources/config/routing.yml"
    prefix:   /api/doc
// ...

```
# app/config/paramters.yml
``` yaml
    restapi_url: 'http://localhost/project/api/v1/'
    oauth2_client_id: ~
    oauth2_client_secret: ~
    oauth2_redirect_url: 'http://localhost/project/authorize'
    oauth2_auth_endpoint: 'http://localhost/project/oauth/v2/auth'
    oauth2_token_endpoint: 'http://localhost/project/oauth/v2/token'
    oauth2_entity_mapping: 'Lincode\RestApi\Bundle\Entity\Member'
    oauth2_entity_repository: 'RestApiBundle:Member'
```

Caso precise mudar a classe padrão de member altere o valor de ´´oauth2_entity_mapping´´
Caso precise mudar a classe padrão de login altere o valor de ´´oauth2_entity_repository´´

# app/config/security.yml
``` yaml
encoders:
    // ...
    Lincode\RestApi\Bundle\Entity\Member: sha512
    // ...

security:
    providers:
        // ...
        user_provider:
            id: platform.user.provider

        members:
            entity: { class: RestApiBundle:Member, property: email }

        // ...

    firewalls:
        // ...
        oauth_token:
            pattern:    ^/oauth/v2/token
            security:   false

        api:
            pattern:    ^/api/v1
            fos_oauth:  true
            stateless:  true

        oauth_authorize:
            pattern:    ^/oauth/v2/auth
            form_login:
                provider: user_provider
                check_path: oauth_server_login_check
                login_path: oauth_server_login
            anonymous: true
        // ...

    access_control:
        // ...
        - { path: ^/api/v1, roles: [ IS_AUTHENTICATED_FULLY ] }
        // ...
```

#### Passo 4: Atualizar Banco e Gerar chaves Oauth

Após configurar a aplicação corretamente atualizar o banco ``php app/console doctrine:schema:update --force`` e gerar
as chaves iniciais para o funcionamendo do Oauth ``php app/console oauth:client:create``, este comando irar gerar uma
saida similar a esta "Added a new client with public id xxx, secret xxx", copie o public id e substitua pelo oauth2_client_id e
o secret pelo oauth2_client_secret.

Feito isso sua applicação ja deve estar rodando corretamente.
Abra em seu navegador a url /api/doc e se tudo estiver correto ja tera uma documentação inicial

#### Passo 5: Gerar REST Controller

Neste momento imagino que tudo ja esteja criado e funcionando perfeitamente, basa ir no console e digitar 
``php app/console bradford:generate:rest`` para gerar o controller do REST.

Obs: Para gerar o controller é preciso ja ter o Form e a Entity