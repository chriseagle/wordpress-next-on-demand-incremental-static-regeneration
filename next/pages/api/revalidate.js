export default async function handler(request, response) {
  if (request.query.secret !== process.env.REVALIDATE_TOKEN) {
    return response.status(401).json({ message: 'Invalid token' })
  }
  try {
    await response.unstable_revalidate(request.query.path)
    return response.json({ revalidated: true })
  } catch (err) {
    return response.status(500).send('Error revalidating')
  }
}