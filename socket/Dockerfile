FROM node:12
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci && npm audit fix
COPY server.js ./server.js
CMD ["node", "/var/www/html/server.js"]
