# 💡 BestIdea — Plataforma para Brainstorming Colaborativo
O **BestIdea** é um ecossistema SaaS voltado para a criação e validação de ideias em salas dinâmicas e temporárias. O projeto permite que usuários criem salas temáticas com contagem regressiva, enviem sugestões de forma anonimizada, votem e comentem.

# 🎯 Filosofia de Desenvolvimento e Stack
Este ecossistema foi desenvolvido utilizando PHP Vanilla, JavaScript Puro (ES6+) e CSS Nativo. A escolha por não utilizar frameworks prontos no core da aplicação foi deliberada, visando o aprendizado profundo de engenharia de software, segurança, gerenciamento de infraestrutura de rede e ciclo de vida de processos.

Para demonstrar proficiência em ferramentas de alta produtividade demandadas pelo mercado corporativo, possuo em paralelo o projeto **[GeralPost](https://github.com/raulbender/geralpost) (Em desenvolvimento)**, construído com o ecossistema completo do Framework Laravel.


# 🧭 Evolução da Frota (Histórico de Projetos)
**O BestIdea é o resultado de um ciclo incremental de aprendizado focado em engenharia backend:**
1. **[BananaClub](https://github.com/raulbender/banana-club) (Legado em Produção na AWS):** Meu primeiro projeto completo de estudo. Embora funcional e hospedado de forma real na AWS, serviu como laboratório para identificar gargalos de escalabilidade, arquivos de estilo monolíticos e oportunidades de melhoria no acoplamento de código PHP.
2. **[Volt R² Framework](https://github.com/raulbender/volt-r2) (Miniframework Autoral):** Desenvolvido como um projeto de fundação pessoal para solucionar as limitações do modelo tradicional (FPM). É um miniframework PHP completo voltado para ambientes de memória persistente com RoadRunner, contendo Container de Injeção de Dependências próprio, Query Builder fluente, Migrations e CLI nativa para automação de tarefas.
3. **BestIdea (SaaS Atual no Render):**  Esta aplicação foi construída inteiramente utilizando o ecossistema do Volt R² Framework, aplicando os conceitos de separação de conceitos na prática.


# 🚀 Decisões de Engenharia & Infraestrutura Backend
- **Arquitetura Residente em Memória (RoadRunner):** A aplicação roda integrada ao servidor RoadRunner (escrito em Go). O framework permanece carregado em memória, eliminando o overhead de inicialização do PHP a cada requisição HTTP convencional e otimizando o tempo de resposta do servidor.
- **Domínio Isolado por Componentes:** Toda a lógica de negócios da aplicação está isolada dentro de App/Components/ (segregada por contextos como Feed, Room e Author). O domínio é independente dos detalhes internos da engine do framework (Framework/).
- **Http Kernel & Ciclo de Vida Próprio:** O fluxo de entrada e saída de requisições é gerenciado por um Http Kernel customizado, responsável por orquestrar Middlewares de segurança, incluindo validação de tokens CSRF e controle de tráfego (Rate Limiter).
- **Stack Focada no PHP:** O foco central do projeto é a robustez do backend. O frontend utiliza JavaScript Vanilla e CSS modular nativo sem o uso de frameworks adicionais, servindo estritamente como camada de integração para dar autonomia completa à aplicação web.



# 🛠️ Tecnologias e Recursos Utilizados
## Backend Core
- **PHP 8.2+ com tipagem estrita (declare(strict_types=1);).**
- **Volt R² Framework:** Miniframework completo desenvolvido do zero para ambientes de runtime persistente.
- **RoadRunner Server:** Servidor de aplicação baseado em corrotinas Go.
- **Redis Connection:** Integração nativa para cache e persistência de sessões no ambiente do RoadRunner.
- **Http Kernel & Ciclo de Vida Próprio:** Orquestração nativa de middlewares, tratamento global de exceções (`ErrorHandler` acoplado ao Sentry)) e emissão estruturada de logs de aplicação (Logger).
- **Engine de Banco de Dados Embutida:** Abstração de banco de dados com PdoAdapter, Query Builder fluente e um sistema nativo de controle de versionamento de esquemas (Migrations).


## Testes & Qualidade de Código
- **PHPUnit:** Suíte de testes automatizados cobrindo testes unitários de componentes (Container, CSRF, Router, Rate Limiter) e testes de integração com o banco de dados.
- **PHP-CS-Fixer & PHPStan:** Análise estática e padronização de código de acordo com as PSRs.


## Integrações e Serviços de Terceiros (Camada de Extensões)
- **Autenticação Unificada (Google OAuth):** Integração nativa com o fluxo de autenticação do Google para onboarding simplificado de usuários.
- **Gateway de Pagamentos (Stripe API):** Arquitetura pronta para processamento de transações e recepção de eventos assíncronos via Webhooks.
- **Camada de Inteligência Artificial (Gemini API):** SDK interno integrado para processamento de linguagem natural e automações inteligentes dentro do ecossistema.
- **Serviço de Mensageria (Resend Mail):** Engine de e-mail integrada para notificações transacionais de alta entrega.


## Monitoramento & Operações
- **Admin Center Integrado:** Painel operacional nativo do framework para controle, auditoria e visualização de logs internos do navio.
- **Observabilidade em Tempo Real (Sentry Integration):** Integração do `ErrorHandler` autoral com o Sentry para captura, agrupamento e alerta imediato de exceções críticas e gargalos em ambiente de produção


## Segurança, Autenticação & Estado
- **Proteção CSRF Nativa:** Middleware e gerador de tokens embutidos no core do framework para blindagem de requisições de escrita (`POST`/`PUT`/`DELETE`), mitigando ataques de falsificação de solicitação entre sites.
- **Rate Limiter Autoral:** Componente integrado para limitação de taxa de requisições, focado na mitigação de abusos na API e proteção contra ataques de força bruta na borda da aplicação.
- **Gerenciamento de Sessões via Redis:** Conector nativo para persistência e compartilhamento de estados de sessão entre múltiplos workers em runtime distribuído (RoadRunner).


## Infraestrutura & Deploy
- **Docker:** Ambientes isolados e replicáveis para desenvolvimento (Dockerfile.dev) e produção (Dockerfile).
- **Render Cloud:** Hospedagem da aplicação focada em deploy contínuo integrado ao repositório.


## 📐 Estrutura de Diretórios
- **A divisão do código reflete o desacoplamento entre o núcleo reutilizável e o domínio da aplicação:**
```text
├── App/
│   ├── Components/        # Domínio de Negócios (Feed, Room, Author) segregados por contexto
│   ├── Database/          # Migrations isolados do domínio
│   └── Views/             # Camada de apresentação organizada por módulos
├── Framework/             # O Motor do Volt R² (Agnóstico e Reutilizável)
│   ├── Database/          # PdoAdapter e Query Builder fluente construído do zero
│   ├── Extensions/        # Conectores de Infraestrutura (AI Client, Stripe, Redis, Mail)
│   ├── Http/              # Http Kernel, Middleware de Sessão e Proteção CSRF
│   └── Tests/             # Suíte de Testes Unitários e de Integração do Framework
