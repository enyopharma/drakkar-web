import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';
import { FaSave, FaEraser } from 'react-icons/fa'

import { AppProps } from '../src/props'
import { InteractorI } from '../src/types'

import { MethodSection } from './MethodSection'
import { InteractorNav } from './InteractorNav'
import { InteractorSection } from './InteractorSection'

export const Form: React.FC<AppProps> = ({ type, method, interactor1, interactor2, actions, ...props }) => {
    const [tab, setTab] = useState<InteractorI>(1)
    const [modal, setModal] = useState<boolean>(false)

    const resetAndScrollTop = () => {
        actions.reset()
        setModal(false)
        const elem = document.getElementById('form-top')
        if (elem) elem.scrollIntoView()
    }

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
            <div id="form-top" className="card">
                <h3 className="card-header">Add a new {type} description</h3>
                <div className="card-body">
                    <MethodSection {...method} {...actions.method} />
                </div>
                <div className="card-header py-0">
                    <InteractorNav type={type} current={tab} update={setTab} />
                </div>
                <div className="card-body">
                    {tab == 1 ? <InteractorSection {...interactor1} {...actions.interactor1} /> : null}
                    {tab == 2 ? <InteractorSection {...interactor2} {...actions.interactor2} /> : null}
                </div>
                <div className="card-footer">
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => actions.save()}
                                disabled={props.saving || !props.savable}
                            >
                                {props.saving
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
                                disabled={!props.resetable}
                            >
                                <FaEraser /> Reset form data
                            </button>
                        </div>
                    </div>
                    {props.feedback == null ? null : (
                        <div className="row">
                            <div className="col">
                                <div className={props.feedback.success ? 'text-success' : 'text-danger'}>
                                    {props.feedback.success
                                        ? <ul><li>Description successfully saved!</li></ul>
                                        : <ul>{props.feedback.errors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                                    }
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
            <Modal visible={modal} dialogClassName="modal-lg">
                <div className="modal-header">
                    <h5 className="modal-title">
                        Reseting form data
                    </h5>
                    <button type="button" className="close" onClick={e => setModal(false)}>
                        &times;
                    </button>
                </div>
                <div className="modal-body">
                    Are you sure you want to reset the form data?
                </div>
                <div className="modal-footer">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => resetAndScrollTop()}
                    >
                        <FaEraser /> Reset form data
                    </button>
                </div>
            </Modal>
        </form>
    )
}
