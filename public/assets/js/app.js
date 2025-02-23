/* global location */
try {
  const page = new URL(location.href).searchParams.get('page')
  if (page === 'debug-log-viewer') {
    import('./components/log-view.js')
    import('./components/sidebar-toggler.js')
  }
} catch (error) {
  console.log(error)
}
