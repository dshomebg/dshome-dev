import { buildConfig } from 'payload'
import { drizzleAdapter } from '@payloadcms/drizzle'
import { lexicalEditor } from '@payloadcms/richtext-lexical'
import path from 'path'
import { fileURLToPath } from 'url'

// Collections
import Users from './src/payload/collections/Users'

const filename = fileURLToPath(import.meta.url)
const dirname = path.dirname(filename)

export default buildConfig({
  // Admin panel configuration
  admin: {
    user: Users.slug,
    importMap: {
      baseDir: path.resolve(dirname),
    },
  },

  // Collections
  collections: [Users],

  // Editor configuration - using Lexical (modern rich text editor)
  editor: lexicalEditor(),

  // Secret for JWT tokens
  secret: process.env.PAYLOAD_SECRET || 'your-secret-key-change-in-production',

  // TypeScript configuration
  typescript: {
    outputFile: path.resolve(dirname, 'payload-types.ts'),
  },

  // Database adapter - using Drizzle with MySQL
  db: drizzleAdapter({
    url: `mysql://${process.env.DATABASE_USER}:${process.env.DATABASE_PASSWORD}@${process.env.DATABASE_HOST}:${process.env.DATABASE_PORT}/${process.env.DATABASE_NAME}`,
  }),

  // Sharp for image processing
  sharp: {
    // Automatically installed with payload
  },
})
