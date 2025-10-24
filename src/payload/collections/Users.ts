import type { CollectionConfig } from 'payload'

const Users: CollectionConfig = {
  slug: 'users',
  auth: true, // Enable authentication for this collection
  admin: {
    useAsTitle: 'email',
    defaultColumns: ['email', 'name', 'roles'],
  },
  access: {
    // Only admins can read, create, update, delete users
    read: ({ req: { user } }) => {
      if (user) {
        return true
      }
      return false
    },
    create: ({ req: { user } }) => {
      if (user && user.roles?.includes('admin')) {
        return true
      }
      return false
    },
    update: ({ req: { user } }) => {
      if (user && user.roles?.includes('admin')) {
        return true
      }
      return false
    },
    delete: ({ req: { user } }) => {
      if (user && user.roles?.includes('admin')) {
        return true
      }
      return false
    },
  },
  fields: [
    {
      name: 'name',
      type: 'text',
      required: true,
      label: 'Име',
    },
    {
      name: 'roles',
      type: 'select',
      hasMany: true,
      defaultValue: ['user'],
      required: true,
      options: [
        {
          label: 'Admin',
          value: 'admin',
        },
        {
          label: 'User',
          value: 'user',
        },
      ],
      label: 'Роли',
    },
  ],
}

export default Users
