FROM node:20-alpine

WORKDIR /app

# Enable corepack for pnpm/yarn usage if desired
RUN corepack enable

# Install dependencies when package files are present
COPY package*.json ./
RUN if [ -f package.json ]; then npm install; fi

COPY . .

EXPOSE 3000
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
