FROM node:12
WORKDIR /var/www/http
COPY package.json package-lock.json ./
RUN npm ci && npm audit fix
COPY server.js ./server.js
CMD ["node", "/var/www/http/server.js"]
