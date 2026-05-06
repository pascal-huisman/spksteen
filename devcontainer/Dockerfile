FROM node:20-slim

RUN apt-get update && apt-get install -y \
    git \
    curl \
    ca-certificates \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# node gebruiker (uid 1000) bestaat al in deze image — hernoem naar claude
RUN usermod -l claude -d /home/claude -m node \
    && groupmod -n claude node

# Zorg dat de .claude map correct eigendom is en maak een user-lokale npm prefix
# zodat `npm i -g` (auto-update van claude-code) zonder root-rechten werkt
RUN mkdir -p /home/claude/.claude /home/claude/.npm-global \
    && chown -R claude:claude /home/claude

WORKDIR /var/www/html
RUN chown -R claude:claude /var/www/html

USER claude

# npm global packages in home dir → auto-update kan schrijven zonder sudo
ENV NPM_CONFIG_PREFIX=/home/claude/.npm-global
ENV PATH=/home/claude/.npm-global/bin:$PATH
ENV SHELL=/bin/bash
ENV NODE_ENV=development

RUN npm install -g @anthropic-ai/claude-code

CMD ["/bin/bash"]