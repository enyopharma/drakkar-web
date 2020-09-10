import React, { useState, useEffect } from 'react'
import Swal from 'sweetalert2'
import withReactContent from 'sweetalert2-react-content'
import { InteractorI, Feedback } from '../src/types'
import { FaSave, FaEraser } from 'react-icons/fa'
import { resetForm, fireSave } from '../src/reducer'
import { useAppSelector, useAction } from '../src/hooks'

import { InteractorNav } from './InteractorNav'
import { MethodFieldset } from './Method/MethodFieldset'
import { ProteinFieldset } from './Protein/ProteinFieldset'
import { SequenceFieldset } from './Sequence/SequenceFieldset'
import { MappingFieldset } from './Mapping/MappingFieldset'

const MySwal = withReactContent(Swal)

export const Form: React.FC = () => {
    const save = useAction(fireSave)
    const reset = useAction(resetForm)
    const props = useAppSelector(state => state)
    const [tab, setTab] = useState<InteractorI>(1)

    const { type, saving, savable, resetable, feedback } = props

    const editing = props.stable_id.trim().length > 0

    const onSave = () => {
        if (editing) {
            MySwal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Are you sure you want to create a new version of description ${props.stable_id}?`,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonColor: '#d33',
            }).then(result => {
                if (result.isConfirmed) save()
            })
        } else {
            save()
        }
    }

    const onReset = () => {
        reset()
        setTab(1)
        document.getElementById('form-top')?.scrollIntoView()
        MySwal.fire({ icon: 'success', text: 'form reseted!' })
    }

    useEffect(() => {
        if (!feedback) return
        if (feedback.success) {
            MySwal.fire({
                icon: 'success',
                text: 'Description successfully saved!',
            })
        } else {
            MySwal.fire({
                icon: 'error',
                title: <p>Something went wrong</p>,
                html: <ul>{feedback.errors.map((e, i) => <li key={i}>{e}</li>)}</ul>,
            })
        }
    }, [feedback])

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
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
                                onClick={() => onSave()}
                                disabled={!savable}
                            >
                                <SaveIcon saving={saving} /> Save description
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={() => onReset()}
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

const FeedbackRow: React.FC<{ feedback: Feedback }> = ({ feedback }) => {
    if (!feedback.success) return null

    return (
        <div className="row">
            <div className="col">
                <div className="text-success">
                    <ul><li>Description successfully saved!</li></ul>
                </div>
            </div>
        </div>
    )
}
