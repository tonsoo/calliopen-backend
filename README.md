# CalliOpen
CalliOpen é um projeto open source de música, focado em gerenciamento, organização e compartilhamento de playlists, álbuns, autores e arquivos de áudio. Ele oferece uma API robusta para integração com clientes web, mobile ou outros sistemas.

---

### Sumário
* Visão Geral
* Funcionalidades
* Estrutura do Projeto
* Estrutura do Banco de Dados
* Principais Models
* Resources
* Principais Rotas da API
* Como rodar localmente
* Testes
* Contribuição
* Licença

---

## Visão Geral
O CalliOpen é uma plataforma para gerenciamento de músicas, playlists, autores e arquivos de áudio, construída com Laravel, Docker e outras tecnologias modernas. O projeto visa ser extensível, seguro e fácil de usar.

---

### Funcionalidades
* Cadastro e autenticação de usuários;
* Gerenciamento de playlists, álbuns, autores e músicas;
* Upload e manipulação de arquivos de áudio;
* Compartilhamento e colaboração em playlists;
* API RESTful documentada via OpenAPI/Swagger;
* Suporte a múltiplos métodos de autenticação.

---

## Estrutura do Projeto

```bash
app/
  Models/          # Modelos Eloquent
  Http/Resources/  # Resources para serialização de dados
  Http/Controllers # Controllers da API
  Services/        # Serviços de domínio
  ...
database/
  migrations/      # Migrations do banco de dados
routes/
  api.php          # Rotas da API
  web.php          # Rotas web
tests/             # Testes unitários e de integração
...
```

---

## Estrutura do Banco de Dados
As principais tabelas do banco de dados (baseado nos models e migrations):

* **users**: Usuários do sistema
* **clients**: Clientes (usuários autenticados)
* **playlists**: Playlists criadas por usuários
* **playlist_songs**: Relação entre playlists e músicas
* **songs**: Músicas cadastradas
* **albums**: Álbuns de músicas
* **authors**: Autores/artistas
* **categories**: Categorias de músicas
* **files**: Arquivos de áudio e capas

Relacionamentos comuns:

* Usuário tem muitas playlists
* Playlist pertence a um usuário (creator)
* Playlist tem muitas músicas (playlist_songs)
* Música pertence a um álbum e pode estar em várias playlists
* Música pode ter vários autores e categorias

---

## Principais Models
* `User` / `Client`: Usuário autenticado, pode criar playlists, favoritar músicas, etc.
* `Playlist`: Representa uma playlist, com campos como uuid, nome, capa, público/privado, etc.
* `PlaylistSong`: Relação entre playlist e música, com informações de ordem e quem adicionou.
* `Song`: Música, com informações de álbum, autores, categorias, arquivo de áudio, etc.
* `Album`: Álbum musical, pode ter várias músicas.
* `Author`: Autor/artista de músicas.
* `Category`: Categoria/estilo musical.
* `File`: Arquivo de áudio ou imagem.

---

## Resources
Os resources são responsáveis por serializar os dados para a API. Exemplos:

* `PlaylistJson`: Serializa playlists, incluindo uuid, creator, cover, nome, duração, músicas, colaboradores, data de criação.
* `PlaylistSongJson`: Serializa músicas dentro de playlists.
* `SongJson`: Serializa músicas, incluindo informações de álbum, autores, categorias, arquivo, etc.
* `ClientJson` e BasicClientJson: Serializam informações de usuários/clientes.
* `AlbumJson`, AuthorJson, AuthorLinkJson: Serializam álbuns e autores.

---

## Principais Rotas da API
Exemplos de endpoints (veja todos na [`Documentação da API`](https://calliopen.com.br/api/documentation)):

* **Autenticação**

    * `POST /api/auth/login` — Login de usuário
    * `POST /api/auth/register` — Registro de usuário

* **Usuário**

    * `GET /api/user/` — Informações do usuário autenticado
    * `GET /api/user/playlists` — Playlists do usuário
    * `GET /api/user/{client:uuid}` — Informações de outro usuário

* **Playlists**

    * `GET /api/user/{client:uuid}/playlists` — Listar playlists de um usuário
    * `POST /api/user/{client:uuid}/playlists/create` — Criar playlist
    * `GET /api/user/{client:uuid}/playlists/{playlist:uuid}` — Detalhes de uma playlist
    * `POST /api/user/{client:uuid}/playlists/{playlist:uuid}/add/{song}` — Adicionar música à playlist
    * `POST /api/user/{client:uuid}/playlists/{playlist:uuid}/remove/{song}` — Remover música da playlist
    * `POST /api/user/{client:uuid}/playlists/{playlist:uuid}/order` — Ordenar músicas da playlist

* **Artistas**

    * `GET /api/artists/` — Listar todos os artistas
    * `GET /api/artists/me` — Artistas do usuário autenticado

---

## Como rodar localmente

```bash
npm install

cp .env.example .env
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate:fresh --seed
```

Acesse a aplicação em http://localhost:9912.

---

## Testes
Para rodar os testes automatizados:

```bash
docker compose exec php php artisan test
```

Os testes estão em Feature e Unit.

---

## Contribuição
Contribuições são bem-vindas! Para contribuir:

1. Fork este repositório
2. Crie uma branch (git checkout -b feature/nome-da-feature)
3. Faça suas alterações e adicione testes
4. Envie um pull request

---

## Licença
Este projeto está licenciado sob a licença MIT. Veja o arquivo LICENSE para mais informações.