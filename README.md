# API Collaborator - By Convenia ❤

API RESTful desenvolvida em **Laravel** com autenticação via **Laravel Passport**, documentação **Swagger**, e ambiente totalmente conteinerizado via **Docker**.

---

## Tecnologias utilizadas

- **PHP 8.3** + **Laravel 12 + Swoole**
- **Docker** e **Docker Compose**
- **MySQL 8**
- **Laravel Passport** (OAuth2)
- **Swagger (OpenAPI)** para documentação
- **PHPUnit** para testes automatizados
- **Mailgun** integrado para envio de email

---

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

---

## Subindo o sistema

```bash
git clone https://github.com/ronierisonsena/convenia.git
cd convenia
docker-compose build
docker-compose up -d
```

Documentação: http://localhost:8000/api/documentation

Usuario para testes:

Login: manager@example.com

senha: 123456

Obs: Para criar usuario do tipo "manager" enviar `type="manager"` no payload de StoreCollaborator 

## Rodando a fila
**Passo necessario para importar o CSV e enviar email**.

Para receber o email ao importar o CSV, deve-se criar um colaborador com email valido, ou atualizar o email do usuario acima. 

```bash
docker exec convenia_swoole_api php artisan queue:work 
```

## Observações
- Nao tive muito tempo para desenvolver, pois estava de mudança para outra cidade, por isso nao pude caprichar um pouco mais, criando os `Contracts` e `Providers` para fazer a inversao da dependencia.
- Poderia ter criado a classe `Collaborator` para estender `Staff` e `Manager` dela e talvez colocado cache em algumas query's

Obrigado pela oportunidade!
❤
