import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import AboutView from '../../views/AboutView.vue'

describe('About View', () => {
  it('renders properly', () => {
    const wrapper = mount(AboutView)
    expect(wrapper.text()).toContain(
      'Job listing demo web application part of an interview process.',
    )
  })
})
