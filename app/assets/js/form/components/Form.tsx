import React, { useState } from 'react'
import { FaSave, FaEraser } from 'react-icons/fa'

import { AppProps } from '../src/props'
import { InteractorI } from '../src/types'
import { useAction } from '../src/hooks'
import { resetForm, fireSave } from '../src/reducer'

import { ResetModal } from './ResetModal'
import { InteractorNav } from './InteractorNav'
import { InteractorSection } from './InteractorSection'
import { MethodFieldset } from './Method/MethodFieldset'

export const Form: React.FC<AppProps> = ({ type, run_id, pmid, savable, resetable, saving, feedback, ...props }) => {
    const save = useAction(fireSave)
    const reset = useAction(resetForm)
    const [tab, setTab] = useState<InteractorI>(1)
    const [modal, setModal] = useState<boolean>(false)

    const resetTabAndForm = () => {
        setTab(1)
        reset()
    }

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
            <ResetModal top="form-top" show={modal} reset={resetTabAndForm} close={() => setModal(false)} />
            <div id="form-top" className="card">
                <h3 className="card-header">Add a new {type} description</h3>
                <div className="card-body">
                    <MethodFieldset {...props.method} />
                </div>
                <div className="card-header py-0">
                    <InteractorNav type={type} current={tab} update={setTab} />
                </div>
                <div className="card-body">
                    {tab == 1 ? <InteractorSection {...props.interactor1} /> : null}
                    {tab == 2 ? <InteractorSection {...props.interactor2} /> : null}
                </div>
                <div className="card-footer">
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => save({ run_id, pmid })}
                                disabled={saving || !savable}
                            >
                                {saving
                                    ? <span className="spinner-border spinner-border-sm"></span>
                                    : <FaSave />
                                }
                                &nbsp;
                                Save description
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => setModal(true)}
                                disabled={!resetable}
                            >
                                <FaEraser /> Reset form data
                            </button>
                        </div>
                    </div>
                    {feedback == null ? null : (
                        <div className="row">
                            <div className="col">
                                <div className={feedback.success ? 'text-success' : 'text-danger'}>
                                    {feedback.success
                                        ? <ul><li>Description successfully saved!</li></ul>
                                        : <ul>{feedback.errors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                                    }
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </form>
    )
}
