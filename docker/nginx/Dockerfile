# syntax=docker/dockerfile:1.7

FROM node:22-bookworm-slim AS frontend_build
WORKDIR /var/www

COPY package.json package-lock.json ./
RUN npm ci && npm install --no-save @rollup/rollup-linux-x64-gnu

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM nginx:1.27-alpine
WORKDIR /var/www

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public ./public
COPY --from=frontend_build /var/www/public/build ./public/build
