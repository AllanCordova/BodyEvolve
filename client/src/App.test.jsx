import { render, screen } from '@testing-library/react'
import { expect, it } from 'vitest'
import App from './App.jsx'

it('sobe o cliente', () => {
  render(<App />)

  expect(screen.getByRole('heading', { name: 'BodyEvolve' })).toBeInTheDocument()
})
