import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

import { AppProps } from '../src/props'

import { MethodFieldset } from './MethodFieldset'
import { InteractorFieldset } from './InteractorFieldset'

export const Form: React.FC<AppProps> = ({ method, interactor1, interactor2, actions, ...props }) => {
    const [modal, setModal] = useState(false)

    const resetAndScrollTop = () => {
        props.reset()
        setModal(false)
        document.getElementById('form-top').scrollIntoView()
    }

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
            <div id="form-top" className="card">
                <h3 className="card-header">
                    Add a new description
                </h3>
                <div className="card-body">
                    <MethodFieldset {...method} {...actions.method} />
                    <InteractorFieldset {...interactor1} {...actions.interactor1} />
                    <InteractorFieldset {...interactor2} {...actions.interactor2} />
                </div>
                <div className="card-footer">
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => props.save()}
                                disabled={props.saving || !props.savable}
                            >
                                {props.saving
                                    ? <span className="spinner-border spinner-border-sm"></span>
                                    : <span className="fas fa-save" />
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
                                <span className="fas fa-eraser"></span> Reset form data
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
                        <span className="fas fa-eraser"></span> Reset form data
                    </button>
                </div>
            </Modal>
        </form>
    )
}
