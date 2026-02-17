import numpy as np
from sklearn.ensemble import IsolationForest
from flask import Flask, request, jsonify

# Initialize Flask app
app = Flask(__name__)

# Placeholder for Isolation Forest model
model = IsolationForest(n_estimators=100, contamination=0.1, random_state=42)

# Example training data (replace with real data)
X_train = np.random.rand(100, 3)  # 100 samples, 3 features
model.fit(X_train)

# Simple post-processing thresholds to catch high-amount transactions
# that occur at a large distance from the last location. Adjust as needed.
SUSPICIOUS_AMOUNT = 95.0
SUSPICIOUS_DISTANCE_KM = 100.0

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    try:
        # Extract features from request
        features = np.array([
            [
                data['amount'],
                data['transactions_last_hour'],
                data['distance_from_last_location']
            ]
        ])

        # Predict using the model
        score = model.decision_function(features)[0]
        is_fraud = model.predict(features)[0] == -1

        # Post-processing rule: if amount is high and distance is large,
        # mark as fraud regardless of model output (simple safety net).
        adjusted_score = float(score)
        adjusted_is_fraud = bool(is_fraud)
        try:
            amt = float(data.get('amount', 0))
            dist = float(data.get('distance_from_last_location', 0))
            if amt >= SUSPICIOUS_AMOUNT and dist >= SUSPICIOUS_DISTANCE_KM:
                adjusted_is_fraud = True
                # push score toward anomalous side (IsolationForest: lower -> more anomalous)
                adjusted_score = min(adjusted_score, -0.5)
        except Exception:
            # if casting fails, keep model values
            pass

        return jsonify({
            'fraud_score': adjusted_score,
            'is_fraud': adjusted_is_fraud
        })
    except KeyError as e:
        return jsonify({'error': f'Missing key: {str(e)}'}), 400

if __name__ == '__main__':
    app.run(debug=True)