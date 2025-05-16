FROM node:23-alpine

WORKDIR /app

COPY ../../frontend .

RUN npm install

EXPOSE 3000

CMD ["npm", "run", "dev"]
