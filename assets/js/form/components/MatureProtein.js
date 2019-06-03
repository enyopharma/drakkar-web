import React, { useReducer } from 'react'

import CoordinateField from './CoordinateField'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const reducer = (state, action) => {
    switch (action.type) {
        case 'set.name':
            return { name: action.name, start: state.start, stop: state.stop }
        break;
        case 'set.start':
            return { name: state.name, start: action.start, stop: state.stop }
        break;
        case 'set.stop':
            return { name: state.name, start: state.start, stop: action.stop }
        break;
        case 'set.coordinates':
            return { name: state.name, start: action.start, stop: action.stop }
        break;
        default:
            throw new Error(`MatureProtein: invalid state ${action.type}.`)
    }
}

const MatureProtein = ({ name, start, stop, protein, update }) => {
    const [state, dispatch] = useReducer(reducer, { name: name, start: start, stop: stop })

    const setName = name => dispatch({ type: 'set.name', name: name })
    const setStart = start => dispatch({ type: 'set.start', start: start })
    const setStop = stop => dispatch({ type: 'set.stop', stop: stop })
    const setCoordinates = (start, stop) => dispatch({ type: 'set.coordinates', start: start, stop: stop })

    const isNameSet = state.name.trim() != ''

    const areCoordinatesSet = state.start != '' && state.stop != ''

    const doesNameExist = protein.matures.filter(m => {
        return m.name == state.name.trim()
    }).length > 0

    const doCoordinatesExist = protein.matures.filter(m => {
        return m.start == state.start
            && m.stop == state.stop
    }).length > 0

    const doesMatureExist = protein.matures.filter(m => {
        return m.name == state.name.trim()
            && m.start == state.start
            && m.stop == state.stop
    }).length > 0

    const isNameValid = ! isNameSet || ! doesNameExist || doesMatureExist

    const areCoordinatesValid = ! areCoordinatesSet || ! doCoordinatesExist || doesMatureExist

    const isMatureValid = isNameSet && areCoordinatesSet
        && (doesMatureExist || (! doesNameExist && ! doCoordinatesExist))

    const handleValidate = () => {
        update({name: state.name.trim(), start: state.start, stop: state.stop})
    }

    const handleReset = () => {
        setCoordinates(1, protein.sequence.length)
    }

    return (
        <React.Fragment>
            {protein.matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                <React.Fragment>
                    <p>
                        Existing sequences on this uniprot entry:
                    </p>
                    <MatureProteinList matures={protein.matures} select={update} />
                </React.Fragment>
            )}
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={state.name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={state.start}
                        set={setStart}
                        max={protein.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={state.stop}
                        set={setStop}
                        max={protein.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleValidate}
                        disabled={! isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            <SubsequenceFormGroup sequence={protein.sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={protein.sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={handleReset}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default MatureProtein;
