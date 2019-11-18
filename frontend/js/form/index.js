import { render } from 'react-dom'

import Form from './components/Form'

window.description = {
    form: (wrapper, container, type, run_id, pmid, state = {}) => {
        render(Form(wrapper, type, run_id, pmid, state), document.getElementById(container))
    }
}
