import React, { useState, useEffect } from 'react'
import Swal from 'sweetalert2'
import withReactContent from 'sweetalert2-react-content'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSave } from '@fortawesome/free-solid-svg-icons/faSave'
import { faEraser } from '@fortawesome/free-solid-svg-icons/faEraser'

import { InteractorI, Feedback } from '../src/types'
import { resetForm, fireSave } from '../src/reducer'
import { useAppSelector, useAction } from '../src/hooks'

import { InteractorNav } from './InteractorNav'
import { MethodFieldset } from './MethodFieldset'
import { ProteinFieldset } from './ProteinFieldset'
import { SequenceFieldset } from './Sequence/SequenceFieldset'
import { MappingFieldset } from './Mapping/MappingFieldset'

const MySwal = withReactContent(Swal)

export const Form: React.FC = () => {
    const save = useAction(fireSave)
    const reset = useAction(resetForm)
    const props = useAppSelector(state => state)
    const [tab, setTab] = useState<InteractorI>(1)

    const { run_id, pmid, id, stable_id, type, saving, savable, resetable, feedback } = props

    return (
        <form id="form-top" onSubmit={e => e.preventDefault()}>
            <div id="form-top" className="card">
                <h3 className="card-header">{type.toUpperCase()} description form</h3>
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
                    {stable_id.trim().length > 0 && (
                        <div className="alert alert-danger">
                            Validating this form will create a new version of description {props.stable_id}!
                        </div>
                    )}
                    <div className="row">
                        <div className="col">
                            <SaveButton stable_id={stable_id} enabled={savable} saving={saving} save={save} />
                        </div>
                        <div className="col">
                            <ResetButton enabled={resetable} reset={() => { reset(); setTab(1) }} />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <PeptidesButton run_id={run_id} pmid={pmid} id={id} />
                        </div>
                    </div>
                    {feedback && <FeedbackRow feedback={feedback} />}
                </div>
            </div>
        </form>
    )
}

type ButtonProps = {
    enabled: boolean
    onClick: () => void
}

const Button: React.FC<ButtonProps> = ({ enabled, onClick, children }) => (
    <button
        type="button"
        className="btn btn-block btn-primary"
        onClick={onClick}
        disabled={!enabled}
    >
        {children}
    </button>
)

type SaveButtonProps = {
    stable_id: string
    enabled: boolean
    saving: boolean
    save: () => void
}

const SaveButton: React.FC<SaveButtonProps> = ({ stable_id, enabled, saving, save }) => {
    const onClick = () => {
        if (stable_id.trim().length > 0) {
            MySwal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Are you sure you want to create a new version of description ${stable_id}?`,
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

    const icon = saving
        ? <span className="spinner-border spinner-border-sm"></span>
        : <FontAwesomeIcon icon={faSave} />

    return (
        <Button enabled={enabled} onClick={onClick}>
            {icon} Save description
        </Button>
    )
}

type ResetButtonProps = {
    enabled: boolean
    reset: () => void
}

const ResetButton: React.FC<ResetButtonProps> = ({ enabled, reset }) => {
    const onClick = () => {
        reset()
        document.getElementById('form-top')?.scrollIntoView()
        MySwal.fire({ icon: 'success', text: 'form reseted!' })
    }

    return (
        <Button enabled={enabled} onClick={onClick}>
            <FontAwesomeIcon icon={faEraser} /> Reset form data
        </Button>
    )
}

type PeptidesButtonProps = {
    run_id: number
    pmid: number
    id: number | null
}

const PeptidesButton: React.FC<PeptidesButtonProps> = ({ run_id, pmid, id }) => {
    const url = `/runs/${run_id}/publications/${pmid}/descriptions/${id}/peptides`

    const classes = id === null
        ? 'btn btn-block btn-primary disabled'
        : 'btn btn-block btn-primary'

    return (
        <a href={url} target="_blank" className={classes}>
            Edit peptides info
        </a>
    )
}

type FeedbackRowProps = {
    feedback: Feedback
}

const FeedbackRow: React.FC<FeedbackRowProps> = ({ feedback }) => {
    useEffect(() => {
        if (feedback.success) {
            MySwal.fire({
                icon: 'success',
                text: 'Description successfully saved!',
            })
        } else {
            MySwal.fire({
                icon: 'error',
                title: <p>Something went wrong</p>,
                html: (
                    <ul className="list-unstyled">
                        {feedback.errors.map((e, i) => <li key={i}>{e}</li>)}
                    </ul>
                ),
            })
        }
    }, [feedback])

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
