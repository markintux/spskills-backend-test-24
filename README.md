# Prova API

Este projeto é uma API desenvolvida para gerenciar provas e questões, criada como parte da seletiva da competição World Skills do estado de SP. A API permite a criação, resposta e finalização de provas, bem como a consulta de estatísticas de desempenho do usuário.

## Tecnologias Utilizadas

- **Laravel 11**: Framework PHP para desenvolvimento de aplicações web.
- **MySQL**: Banco de dados relacional.
- **PHP**: Linguagem de programação para o backend.
- **JSON**: Formato para armazenamento e transporte de dados.

## Instalação

1. **Clone o repositório:**

    ```bash
    git clone https://github.com/seu-usuario/seu-repositorio.git
    ```

2. **Navegue até o diretório do projeto:**

    ```bash
    cd seu-repositorio
    ```

3. **Instale as dependências do Laravel:**

    ```bash
    composer install
    ```

4. **Copie o arquivo de ambiente de exemplo e ajuste as configurações:**

    ```bash
    cp .env.example .env
    ```

    Edite o arquivo `.env` para configurar a conexão com o banco de dados e outras variáveis de ambiente.

5. **Gere a chave de aplicativo:**

    ```bash
    php artisan key:generate
    ```

6. **Execute as migrations para criar as tabelas no banco de dados:**

    ```bash
    php artisan migrate
    ```

7. **Inicie o servidor embutido do Laravel:**

    ```bash
    php artisan serve
    ```

## Rotas e Métodos

### UsuarioController

- **POST /registro**: Cadastra um novo usuário.
    - **Corpo da Requisição:**
      ```json
      {
          "nome": "João da Silva",
          "username": "joaodasilva",
          "email": "joao.silva@example.com",
          "senha": "senha123"
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Cadastro realizado com sucesso",
          "usuario": {
              "id": 1,
              "nome": "João da Silva",
              "username": "joaodasilva",
              "email": "joao.silva@example.com",
              "token": null,
              "created_at": "2024-09-18T00:00:00.000000Z",
              "updated_at": "2024-09-18T00:00:00.000000Z"
          }
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "mensagem": "Erro ao cadastrar, verifique os dados"
      }
      ```

- **POST /login**: Realiza o login do usuário e gera um token de sessão.
    - **Corpo da Requisição:**
      ```json
      {
          "username": "joaodasilva",
          "senha": "senha123"
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "token": "e99a18c428cb38d5f260853678922e03"
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "mensagem": "Dados incorretos"
      }
      ```

- **GET /logout**: Realiza o logout do usuário e invalida o token.
    - **Parâmetros de Consulta:**
      ```plaintext
      token=e99a18c428cb38d5f260853678922e03
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Logout realizado com sucesso"
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "mensagem": "Usuário inválido"
      }
      ```

### ProvaController

- **POST /prova**: Gera uma nova prova.
    - **Corpo da Requisição:**
      ```json
      {
          "token": "e99a18c428cb38d5f260853678922e03",
          "dificuldade": "Fácil",
          "quantidade_questoes": 5,
          "materias": ["Matemática", "Geografia"]
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Prova gerada com sucesso!",
          "prova": {
              "id": 1,
              "questoes": [
                  {
                      "id": 20,
                      "enunciado": "Qual é o maior oceano do planeta?",
                      "materia": "Geografia",
                      "dificuldade": "Fácil",
                      "opcoes": [
                          {"letra": "A", "opcao": "Oceano Pacífico", "opcao_correta": 1},
                          {"letra": "B", "opcao": "Oceano Atlântico", "opcao_correta": 0}
                      ]
                  }
              ],
              "finalizada": false,
              "dificuldade": "Fácil",
              "quantidade_questoes": 5,
              "materias": ["Matemática", "Geografia"],
              "usuario": 1
          }
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "erro": "Erro ao gerar a prova",
          "mensagem": {
              "dificuldade": ["O campo dificuldade é obrigatório."],
              "quantidade_questoes": ["O campo quantidade questoes é obrigatório."],
              "materias": ["O campo materias é obrigatório."]
          }
      }
      ```

- **POST /prova/responder**: Registra a resposta do usuário para uma questão da prova.
    - **Corpo da Requisição:**
      ```json
      {
          "token": "e99a18c428cb38d5f260853678922e03",
          "prova_id": 1,
          "questao_id": 20,
          "resposta": "A"
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Resposta registrada com sucesso!",
          "prova": {
              "id": 1,
              "questoes": [
                  {
                      "id": 20,
                      "enunciado": "Qual é o maior oceano do planeta?",
                      "materia": "Geografia",
                      "dificuldade": "Fácil",
                      "opcoes": [
                          {"letra": "A", "opcao": "Oceano Pacífico", "opcao_correta": 1},
                          {"letra": "B", "opcao": "Oceano Atlântico", "opcao_correta": 0}
                      ],
                      "resposta_usuario": "A"
                  }
              ],
              "finalizada": false,
              "dificuldade": "Fácil",
              "quantidade_questoes": 5,
              "materias": ["Matemática", "Geografia"],
              "usuario": 1
          }
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "erro": "Erro ao enviar a resposta",
          "mensagem": {
              "resposta": ["A resposta deve ser uma das opções válidas: A, B, C, D, E."]
          }
      }
      ```

- **POST /prova/finalizar**: Finaliza a prova.
    - **Corpo da Requisição:**
      ```json
      {
          "token": "e99a18c428cb38d5f260853678922e03",
          "prova_id": 1
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Prova finalizada com sucesso!"
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "erro": "Erro ao finalizar a prova",
          "mensagem": {
              "prova_id": ["O campo prova id é obrigatório."]
          }
      }
      ```

- **POST /prova/consultar**: Consulta uma prova específica.
    - **Corpo da Requisição:**
      ```json
      {
          "token": "e99a18c428cb38d5f260853678922e03",
          "prova_id": 1
      }
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Prova consultada com sucesso!",
          "prova": {
              "id": 1,
              "questoes": [
                  {
                      "id": 20,
                      "enunciado": "Qual é o maior oceano do planeta?",
                      "materia": "Geografia",
                      "dificuldade": "Fácil",
                      "opcoes": [
                          {"letra": "A", "opcao": "Oceano Pacífico", "opcao_correta": 1},
                          {"letra": "B", "opcao": "Oceano Atlântico", "opcao_correta": 0}
                      ]
                  }
              ],
              "finalizada": false,
              "dificuldade": "Fácil",
              "quantidade_questoes": 5,
              "materias": ["Matemática", "Geografia"],
              "usuario": 1
          }
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "erro": "Erro ao consultar a prova",
          "mensagem": {
              "prova_id": ["A prova solicitada não foi encontrada."]
          }
      }
      ```

- **GET /metricas**: Obtém estatísticas de desempenho do usuário.
    - **Parâmetros de Consulta:**
      ```plaintext
      token=e99a18c428cb38d5f260853678922e03
      ```
    - **Resposta de Sucesso:**
      ```json
      {
          "mensagem": "Estatísticas obtidas com sucesso!",
          "metricas": {
              "total_provas": 10,
              "questoes_corretas": 25,
              "questoes_erradas": 5,
              "estatisticas_por_materia": {
                  "Matemática": {"corretas": 10, "erradas": 2},
                  "Geografia": {"corretas": 15, "erradas": 3}
              }
          }
      }
      ```
    - **Resposta de Erro:**
      ```json
      {
          "erro": "Erro ao obter estatísticas",
          "mensagem": {
              "token": ["Token inválido ou expirado."]
          }
      }
      ```

## Contribuição

Sinta-se à vontade para contribuir com melhorias ou correções. Basta fazer um fork do repositório, realizar suas alterações e enviar um pull request.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

## Postman Collection

Para facilitar os testes da API, incluímos uma coleção do Postman no corpo do projeto. O arquivo se chama `SPSkills.postman_collection.json`. Basta importá-lo para o Postman para testar todos os endpoints da API.

---

Para qualquer dúvida ou ajuda adicional, entre em contato através dos canais de suporte ou abra uma issue no repositório.