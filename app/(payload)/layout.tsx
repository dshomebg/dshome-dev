/* THIS FILE WAS GENERATED AUTOMATICALLY BY PAYLOAD. */
import type { Metadata } from 'next'

import config from '@payload-config'
import { RootLayout } from '@payloadcms/next/layouts'

import './custom.scss'

type Args = {
  children: React.ReactNode
}

export const metadata: Metadata = {
  title: 'Payload Admin - dshome.dev',
  description: 'Admin панел за управление на dshome.dev',
}

const Layout = ({ children }: Args) => (
  <RootLayout config={config}>
    {children}
  </RootLayout>
)

export default Layout
