import React, { useState } from 'react'
import { InteractorI, Feedback } from '../src/types'
import { FaSave, FaEraser } from 'react-icons/fa'
import { resetForm, fireSave } from '../src/reducer'
import { useAppSelector, useAction } from '../src/hooks'

import { SaveModal } from './SaveModal'
import { ResetModal } from './ResetModal'
import { InteractorNav } from './InteractorNav'
import { MethodFieldset } from './Method/MethodFieldset'
import { ProteinFieldset } from './Protein/ProteinFieldset'
import { SequenceFieldset } from './Sequence/SequenceFieldset'
import { MappingFieldset } from './Mapping/MappingFieldset'

export const Form: React.FC = () => {
    const save = useAction(fireSave)
    const reset = useAction(resetForm)
    const props = useAppSelector(state => state)
    const [tab, setTab] = useState<InteractorI>(1)
    const [showSave, setSaveModal] = useState<boolean>(false)
    const [showReset, setResetModal] = useState<boolean>(false)

    const { type, saving, savable, resetable, feedback } = props

    const editing = props.stable_id.trim().length > 0

    const onSave = () => {
        editing
            ? setSaveModal(true)
            : save()
    }

    const onReset = () => {
        setResetModal(true)
    }

    const saveAndClose = () => {
        save()
        setSaveModal(false)
    }

    const resetAndClose = () => {
        reset()
        setTab(1)
        setResetModal(false)
        document.getElementById('form-top')?.scrollIntoView()
    }

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
            <SaveModal stable_id={props.stable_id} show={showSave} save={saveAndClose} close={() => setSaveModal(false)} />
            <ResetModal show={showReset} reset={resetAndClose} close={() => setResetModal(false)} />
            <div id="form-top" className="card">
                <h3 className="card-header">Add a new {type} description</h3>
                <div className="card-body">
                    <MethodFieldset />
                </div>
                <div className="card-header py-0">
                    <InteractorNav type={type} current={tab} update={setTab} />
                </div>
                <div className="card-body">
                    <ProteinFieldset i={tab} />
                    <SequenceFieldset i={tab} />
                    <MappingFieldset i={tab} />
                </div>
                <div className="card-footer">
                    {editing && (
                        <div className="alert alert-danger">
                            Validating this form will create a new version of description {props.stable_id}!
                        </div>
                    )}
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => onSave()}
                                disabled={!savable}
                            >
                                <SaveIcon saving={saving} /> Save description
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={e => onReset()}
                                disabled={!resetable}
                            >
                                <FaEraser /> Reset form data
                            </button>
                        </div>
                    </div>
                    {feedback && <FeedbackRow feedback={feedback} />}
                </div>
            </div>
        </form>
    )
}

const SaveIcon: React.FC<{ saving: boolean }> = ({ saving }) => saving
    ? <span className="spinner-border spinner-border-sm"></span>
    : <FaSave />

const FeedbackRow: React.FC<{ feedback: Feedback }> = ({ feedback }) => (
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
)
