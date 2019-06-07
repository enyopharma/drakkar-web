import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

const ActionsFieldset = ({ saving, savable, save, resetable, reset, feedback }) => {
    const [modal, setModal] = useState(false)

    const confirmReset = () => {
        reset()
        setModal(false)
        window.scrollTo(0, 0)
    }

    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-warning'} />
                &nbsp;
                Actions
            </legend>
            <div className="row">
                <div className="col-6">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => save()}
                        disabled={saving || ! savable}
                    >
                        {saving
                            ? <span className="spinner-border spinner-border-sm"></span>
                            : <i className="fas fa-save" />
                        }
                        &nbsp;
                        Save description
                    </button>
                </div>
                <div className="col-6">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => setModal(true)}
                        disabled={! resetable}
                    >
                        <i className="fas fa-eraser" /> Reset form data
                    </button>
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
                        onClick={e => confirmReset()}
                    >
                        <i className="fas fa-eraser" /> Reset form data
                    </button>
                </div>
            </Modal>
            {feedback == null ? null : (
                <p className={feedback.success ? 'text-success' : 'text-danger'}>
                    {feedback.success
                        ? 'Description successfully saved!'
                        : feedback.message
                    }
                </p>
            )}
        </fieldset>
    )
}

export default ActionsFieldset
