# 💡 BestIdea
![Build Status](https://github.com/raulbender/bestidea/actions/workflows/main.yml/badge.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)
![PHPStan](https://img.shields.io/badge/phpstan-level%209-brightgreen.svg)

[🇺🇸 English Version](#-english-version) | [🇧🇷 Versão em Português](#-versão-em-português)

---

## 🇺🇸 English Version

### 💡 BestIdea — Collaborative Brainstorming Platform
BestIdea is a SaaS application developed for creating and validating ideas in dynamic, temporary rooms. The system allows users to create themed rooms with countdown timers, submit anonymous suggestions, vote, and comment.

### 🎯 Development Philosophy & Stack
This project was developed using Vanilla PHP, Pure JavaScript (ES6+), and Native CSS. The decision not to use ready-made frameworks in the core application was deliberate, aimed at gaining a deep understanding of software engineering, web security, process lifecycles, and infrastructure.

To practice with production-ready tools heavily demanded by the market, I also maintain the **[GeralPost](https://github.com/raulbender/geralpost)** project (under development), built using the Laravel Framework ecosystem.

### 🧭 Project Evolution & Background
**BestIdea is the result of an incremental learning cycle focused on backend engineering:**
1. **[BananaClub](https://github.com/raulbender/banana-club) (Legacy in Production on AWS):** My first complete study project. While functional and hosted on AWS, it served as a laboratory to identify architectural bottlenecks and code coupling opportunities.
2. **[Volt R² Framework](https://github.com/raulbender/volt-r2) (Custom Mini-framework):** Built as a personal foundation project to move away from the traditional PHP-FPM model. It is a mini-framework designed for persistent in-memory environments using RoadRunner, containing a native Dependency Injection Container, fluent Query Builder, Migrations, and a custom CLI.
3. **BestIdea (Current SaaS on Render):** This application was built entirely on top of the Volt R² Framework ecosystem, practicing the separation of concerns in a real-world scenario.

### 🚀 Engineering Decisions & Backend Infrastructure
*   **In-Memory Architecture (RoadRunner):** The application runs integrated with the RoadRunner server (Go). The framework remains loaded in memory, avoiding PHP's bootstrap overhead on every HTTP request.
*   **Component-Based Domain Architecture:** All business logic is isolated within `App/Components/` (segregated by contexts like Feed, Room, and Author). The domain remains independent of the internal framework engine (`Framework/`).
*   **Custom HTTP Kernel:** The request/response lifecycle is managed by a custom HTTP Kernel, which orchestrates security Middlewares, including CSRF token validation and traffic control (Rate Limiter).
*   **Backend-Focused Stack:** The core focus is backend robustness. The frontend uses Vanilla JavaScript and native modular CSS, serving strictly as an integration layer.

### 🛠️ Technologies & Features
#### Backend Core
*   **PHP 8.2+** with strict typing enabled (`declare(strict_types=1);`).
*   **Volt R² Framework:** Custom mini-framework for persistent runtime environments.
*   **RoadRunner Server:** High-performance application server based on Go coroutines.
*   **Redis Connection:** Native integration for session persistence and caching within RoadRunner workers.
*   **HTTP Kernel & Lifecycle:** Native middleware orchestration, global exception handling (`ErrorHandler` integrated with Sentry), and structured logging (`Logger`).
*   **Database Engine:** Database abstraction layer with `PdoAdapter`, fluent Query Builder, and a native schema migration system.

#### Testing & Code Quality
*   **PHPUnit:** Automated test suite covering unit tests for core components (Container, CSRF, Router, Rate Limiter) and database integration tests.
*   **PHP-CS-Fixer & PHPStan:** Static analysis and code standardization running strictly at **PHPStan Level 9**.

#### Third-Party Integrations & Extensions
*   **Unified Authentication (Google OAuth):** Native integration with Google Auth for user onboarding.
*   **Payment Gateway (Stripe API):** Architecture prepared for transaction processing and handling asynchronous events via Webhooks.
*   **Artificial Intelligence Layer (Gemini API):** Internal SDK integration for natural language processing tasks.
*   **Email Service (Resend Mail):** Integrated transactional email engine.

#### Security & Observability
*   **Native CSRF Protection:** Embedded middleware and token generation guarding all `POST`/`PUT`/`DELETE` endpoints.
*   **Custom Rate Limiter:** Integrated component to limit request rates and mitigate API abuse or brute-force attempts.
*   **Real-Time Observability (Sentry):** Integrated global exception handler for immediate error tracking in production.
*   **Admin Center:** Operational dashboard built into the framework to view internal application logs.

#### Infrastructure
*   **Docker:** Isolated environments for development (`Dockerfile.dev`) and production (`Dockerfile`).
*   **Render Cloud:** Application hosting focused on continuous deployment directly from the repository.

---

## 🇧🇷 Versão em Português

### 💡 BestIdea — Plataforma para Brainstorming Colaborativo
O **BestIdea** é uma aplicação SaaS voltada para a criação e validação de ideias em salas dinâmicas e temporárias. O projeto permite que usuários criem salas temáticas com contagem regressiva, enviem sugestões de forma anonimizada, votem e comentem.

### 🎯 Filosofia de Desenvolvimento e Stack
Este projeto foi desenvolvido utilizando PHP Vanilla, JavaScript Puro (ES6+) e CSS Nativo. A escolha por não utilizar frameworks prontos no core da aplicação foi deliberada, visando o aprendizado profundo de engenharia de software, segurança, ciclo de vida de processos e infraestrutura.

Para praticar com ferramentas de alta produtividade demandadas pelo mercado corporativo, possuo em paralelo o projeto **[GeralPost](https://github.com/raulbender/geralpost)** (em desenvolvimento), construído com o ecossistema completo do Framework Laravel.

### 🧭 Evolução da Frota (Histórico de Projetos)
**O BestIdea é o resultado de um ciclo incremental de aprendizado focado em engenharia backend:**
1. **[BananaClub](https://github.com/raulbender/banana-club) (Legado em Produção na AWS):** Meu primeiro projeto completo de estudo. Embora funcional e hospedado de forma real na AWS, serviu como laboratório para identificar gargalos de acoplamento de código e oportunidades de melhoria arquitetural.
2. **[Volt R² Framework](https://github.com/raulbender/volt-r2) (Miniframework Autoral):** Desenvolvido como um projeto de fundação pessoal para solucionar as limitações do modelo tradicional (FPM). É um miniframework PHP voltado para ambientes de memória persistente com RoadRunner, contendo Container de Injeção de Dependências próprio, Query Builder fluente, Migrations e CLI nativa.
3. **BestIdea (SaaS Atual no Render):** Esta aplicação foi construída inteiramente utilizando o ecossistema do Volt R² Framework, aplicando os conceitos de separação de conceitos na prática.

### 🚀 Decisões de Engenharia & Infraestrutura Backend
*   **Arquitetura Residente em Memória (RoadRunner):** A aplicação roda integrada ao servidor RoadRunner (escrito em Go). O framework permanece carregado em memória, eliminando o overhead de inicialização do PHP a cada requisição HTTP convencional.
*   **Domínio Isolado por Componentes:** Toda a lógica de negócios da aplicação está isolada dentro de `App/Components/` (segregada por contextos como Feed, Room e Author). O domínio é independente dos detalhes internos da engine do framework (`Framework/`).
*   **Http Kernel & Ciclo de Vida Próprio:** O fluxo de entrada e saída de requisições é gerenciado por um Http Kernel customizado, responsável por orquestrar Middlewares de segurança, incluindo validação de tokens CSRF e controle de tráfego (Rate Limiter).
*   **Stack Focada no PHP:** O foco central do projeto é a robustez do backend. O frontend utiliza JavaScript Vanilla e CSS modular nativo sem o uso de frameworks adicionais, servindo estritamente como camada de integração.

### 🛠️ Tecnologias e Recursos Utilizados
#### Backend Core
*   **PHP 8.2+** com tipagem estrita (`declare(strict_types=1);`).
*   **Volt R² Framework:** Miniframework desenvolvido para ambientes de runtime persistente.
*   **RoadRunner Server:** Servidor de aplicação baseado em corrotinas Go.
*   **Redis Connection:** Integração nativa para cache e persistência de sessões no ambiente do RoadRunner.
*   **Http Kernel & Ciclo de Vida Próprio:** Orquestração nativa de middlewares, tratamento global de exceções (`ErrorHandler` acoplado ao Sentry) e emissão estruturada de logs de aplicação (`Logger`).
*   **Engine de Banco de Dados Embutida:** Abstração de banco de dados com `PdoAdapter`, Query Builder fluente e sistema nativo de controle de versionamento de esquemas (Migrations).

#### Testes & Qualidade de Código
*   **PHPUnit:** Suíte de testes automatizados cobrindo testes unitários de componentes (Container, CSRF, Router, Rate Limiter) e testes de integração com o banco de dados.
*   **PHP-CS-Fixer & PHPStan:** Análise estática e padronização de código executada rigidamente no nível **PHPStan Level 9**.

#### Integrações e Serviços de Terceiros (Camada de Extensões)
*   **Autenticação Unificada (Google OAuth):** Integração nativa com o fluxo de autenticação do Google para onboarding de usuários.
*   **Gateway de Pagamentos (Stripe API):** Arquitetura pronta para processamento de transações e recepção de eventos assíncronos via Webhooks.
*   **Camada de Inteligência Artificial (Gemini API):** SDK interno integrado para tarefas de processamento de linguagem natural.
*   **Serviço de Mensageria (Resend Mail):** Engine de e-mail integrada para notificações transacionais.

#### Segurança & Observabilidade
*   **Proteção CSRF Nativa:** Middleware e gerador de tokens embutidos no core do framework para blindagem de requisições de escrita (`POST`/`PUT`/`DELETE`).
*   **Rate Limiter Autoral:** Componente integrado para limitação de taxa de requisições, focado na proteção contra abusos na API e ataques de força bruta.
*   **Observabilidade em Tempo Real (Sentry):** Integração do `ErrorHandler` global com o Sentry para monitoramento e captura de exceções críticas em ambiente de produção.
*   **Admin Center Integrado:** Painel operacional nativo do framework para visualização de logs internos da aplicação.

#### Infraestrutura & Deploy
*   **Docker:** Ambientes isolados para desenvolvimento (`Dockerfile.dev`) and produção (`Dockerfile`).
*   **Render Cloud:** Hospedagem da aplicação focada em deploy contínuo integrado ao repositório.

---

### 📐  Directory Structure / Estrutura de Diretórios
```text
├── App/
│   ├── Components/        # Business Domain / Domínio de Negócios (Feed, Room, Author)
│   ├── Database/          # Isolated Migrations / Migrations isolados do domínio
│   └── Views/             # Presentation Layer / Camada de apresentação por módulos
├── Framework/             # Volt R² Engine / O Motor do Volt R² (Agnóstico e Reutilizável)
│   ├── Database/          # PdoAdapter & Fluent Query Builder from scratch / construídos do zero
│   ├── Extensions/        # Infra Connectors / Conectores (AI Client, Stripe, Redis, Mail)
│   ├── Http/              # Http Kernel, Session Middleware & CSRF Protection
│   └── Tests/             # Framework Unit & Integration Tests / Testes do Framework