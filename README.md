# 🚀 API Collaborator - By Convenia ❤

API RESTful desenvolvida em **Laravel** com autenticação via **Laravel Passport**, documentação **Swagger**, e ambiente totalmente conteinerizado via **Docker**.

---

## 🧩 Tecnologias utilizadas

- **PHP 8.3** + **Laravel 11 + Swoole**
- **Docker** e **Docker Compose**
- **MySQL 8**
- **Laravel Passport** (OAuth2)
- **Swagger (OpenAPI)** para documentação
- **PHPUnit** para testes automatizados

---

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)
- (Opcional) [Make](https://www.gnu.org/software/make/) para comandos simplificados

---

## Subindo o sistema

```bash
git clone git clone git@github.com:ronierisonsena/convenia.git
cd convenia
docker-compose up -d --build
```

Usuario para testes:

Login: manager@example.com

senha: 123456

## Rodando a fila

```bash
docker exec convenia_swoole_api php artisan queue:work 
```
