"use client";
import React from "react";
import AppHeader from "@/layout/AppHeader";
import AppSidebar from "@/layout/AppSidebar";
import Backdrop from "@/layout/Backdrop";
import { useSidebar } from "@/context/SidebarContext";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { isMobileOpen, isOpen } = useSidebar();

  return (
    <div className="flex h-screen overflow-hidden">
      {/* Sidebar */}
      <AppSidebar />

      {/* Content Area */}
      <div className="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
        {/* Header */}
        <AppHeader />

        {/* Main Content */}
        <main className="mx-auto w-full max-w-screen-2xl p-4 md:p-6 2xl:p-10">
          {children}
        </main>
      </div>

      {/* Mobile Backdrop */}
      {isMobileOpen && <Backdrop />}
    </div>
  );
}
